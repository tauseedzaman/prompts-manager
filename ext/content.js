// content.js

console.log('Prompt Manager Companion active');

// Function to inject the floating button or sidebar trigger
function injectUI() {
    if (document.getElementById('pm-trigger')) return;

    const trigger = document.createElement('div');
    trigger.id = 'pm-trigger';
    trigger.innerHTML = '✨';
    trigger.title = 'Open Prompt Manager';
    document.body.appendChild(trigger);

    trigger.addEventListener('click', toggleSidebar);
}

let sidebar = null;

function toggleSidebar() {
    if (sidebar) {
        sidebar.remove();
        sidebar = null;
        return;
    }

    sidebar = document.createElement('div');
    sidebar.id = 'pm-sidebar';
    sidebar.innerHTML = `
        <div class="pm-sidebar-header">
            <h3>Prompts</h3>
            <button id="pm-close">✕</button>
        </div>
        <div class="pm-sidebar-search">
            <input type="text" id="pm-search" placeholder="Search prompts...">
        </div>
        <div id="pm-list" class="pm-list">
            <p>Loading...</p>
        </div>
    `;
    document.body.appendChild(sidebar);

    document.getElementById('pm-close').onclick = () => {
        sidebar.remove();
        sidebar = null;
    };

    const searchInput = document.getElementById('pm-search');
    searchInput.oninput = debounce(() => {
        loadPrompts(searchInput.value);
    }, 300);

    loadPrompts();
}

async function loadPrompts(search = '') {
    const list = document.getElementById('pm-list');
    if (!list) return;

    const response = await chrome.runtime.sendMessage({
        type: 'FETCH_PROMPTS',
        payload: { search }
    });

    if (response.error) {
        list.innerHTML = `<p class="pm-error">Error: ${response.error}. Check extension settings.</p>`;
        return;
    }

    const prompts = response.data;
    if (!prompts || prompts.length === 0) {
        list.innerHTML = '<p>No prompts found.</p>';
        return;
    }

    list.innerHTML = '';
    prompts.forEach(prompt => {
        const item = document.createElement('div');
        item.className = 'pm-item';
        item.innerHTML = `
            <div class="pm-item-title">${prompt.title}</div>
            <div class="pm-item-text">${prompt.description || truncate(prompt.prompt_text)}</div>
        `;
        item.onclick = () => usePrompt(prompt.prompt_text);
        list.appendChild(item);
    });
}

function usePrompt(text) {
    // Try to find ChatGPT input
    const input = document.querySelector('div[id="prompt-textarea"]') ||
        document.querySelector('textarea') ||
        document.activeElement;

    if (input) {
        // For ContentEditable (ChatGPT default)
        if (input.contentEditable === 'true') {
            input.focus();
            document.execCommand('insertText', false, text);
        } else {
            input.value = text;
            input.dispatchEvent(new Event('input', { bubbles: true }));
        }
    } else {
        copyToClipboard(text);
        alert('Prompt copied to clipboard (could not find input field)');
    }
}

function truncate(text, length = 60) {
    if (text.length <= length) return text;
    return text.substring(0, length) + '...';
}

function copyToClipboard(text) {
    navigator.clipboard.writeText(text);
}

function debounce(func, wait) {
    let timeout;
    return function (...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), wait);
    };
}

// Watch for DOM changes to re-inject if needed
const observer = new MutationObserver(() => injectUI());
observer.observe(document.body, { childList: true, subtree: true });

injectUI();

// Add "Save to Manager" buttons to chat messages
function addSaveButtons() {
    // Standard ChatGPT message bubbles
    const messages = document.querySelectorAll('div[data-testid^="conversation-turn-"]');
    messages.forEach(msg => {
        if (msg.querySelector('.pm-save-btn')) return;

        const actionRow = msg.querySelector('.flex.justify-between') || msg.querySelector('.actions');
        if (actionRow) {
            const btn = document.createElement('button');
            btn.className = 'pm-save-btn';
            btn.textContent = '💾 Save to prompts';
            btn.onclick = () => saveMessageToPrompts(msg);
            actionRow.appendChild(btn);
        }
    });
}

async function saveMessageToPrompts(msgElement) {
    const textElement = msgElement.querySelector('.markdown') || msgElement.querySelector('.whitespace-pre-wrap');
    if (!textElement) return;

    const text = textElement.innerText;
    const response = await chrome.runtime.sendMessage({
        type: 'SAVE_PROMPT',
        payload: { text }
    });

    if (response.error) {
        alert('Error: ' + response.error);
    } else {
        const btn = msgElement.querySelector('.pm-save-btn');
        btn.textContent = '✅ Saved!';
        setTimeout(() => btn.textContent = '💾 Save to prompts', 2000);
    }
}

setInterval(addSaveButtons, 2000);
