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
let currentTab = 'prompts';

async function toggleSidebar() {
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
        <div class="pm-filter-bar">
            <select id="pm-workspace" class="w-full">
                <option value="">Private Library</option>
            </select>
            <select id="pm-sort">
                <option value="latest">Latest</option>
                <option value="most_used">Most Used</option>
            </select>
        </div>
        <div class="pm-tabs">
            <button class="pm-tab-btn active" data-tab="prompts">My</button>
            <button class="pm-tab-btn" data-tab="favorites">Favs</button>
            <button class="pm-tab-btn" data-tab="marketplace">Store</button>
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

    const sortSelect = sidebar.querySelector('#pm-sort');
    sortSelect.onchange = () => loadPrompts(searchInput.value, sortSelect.value);

    const workspaceSelect = sidebar.querySelector('#pm-workspace');
    workspaceSelect.onchange = async () => {
        await chrome.storage.local.set({ lastWorkspace: workspaceSelect.value });
        loadPrompts(searchInput.value, sortSelect.value, workspaceSelect.value);
    };

    const tabBtns = sidebar.querySelectorAll('.pm-tab-btn');
    tabBtns.forEach(btn => {
        btn.onclick = () => {
            tabBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            currentTab = btn.dataset.tab;
            loadPrompts(searchInput.value, sortSelect.value, workspaceSelect.value);
        };
    });

    loadWorkspaces(workspaceSelect);

    const { lastWorkspace } = await chrome.storage.local.get('lastWorkspace');
    if (lastWorkspace) workspaceSelect.value = lastWorkspace;

    loadPrompts(searchInput.value, sortSelect.value, workspaceSelect.value);
}

async function loadWorkspaces(selectElement) {
    try {
        const response = await chrome.runtime.sendMessage({ type: 'FETCH_WORKSPACES' });
        if (response.error) throw new Error(response.error);

        const { workspaces } = response;
        selectElement.innerHTML = '<option value="">Private Library</option>';
        workspaces.forEach(ws => {
            const opt = document.createElement('option');
            opt.value = ws.id;
            opt.textContent = ws.name;
            selectElement.appendChild(opt);
        });

        const { lastWorkspace } = await chrome.storage.local.get('lastWorkspace');
        if (lastWorkspace) selectElement.value = lastWorkspace;
    } catch (error) {
        console.error('Failed to load workspaces in sidebar:', error);
    }
}

async function loadPrompts(search = '', sort = 'latest', workspace_id = '') {
    const list = document.getElementById('pm-list');
    if (!list) return;

    const payload = { search };
    if (currentTab === 'favorites') payload.is_favorite = true;
    if (currentTab === 'marketplace') payload.marketplace = true;
    payload.sort = sort;
    if (workspace_id) payload.workspace_id = workspace_id;

    const response = await chrome.runtime.sendMessage({
        type: 'FETCH_PROMPTS',
        payload
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
            <div class="pm-item-content">
                <div class="pm-item-title">${prompt.title}</div>
                <div class="pm-item-text">${prompt.description || truncate(prompt.prompt_text)}</div>
            </div>
            ${currentTab !== 'marketplace' ? `
            <button class="pm-delete-btn" title="Delete Prompt">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2M10 11v6M14 11v6"/></svg>
            </button>` : ''}
        `;

        item.querySelector('.pm-item-content').onclick = () => {
            usePrompt(prompt.prompt_text);
            // Track usage
            chrome.runtime.sendMessage({
                type: 'INCREMENT_USAGE',
                payload: { id: prompt.id }
            });
        };

        const delBtn = item.querySelector('.pm-delete-btn');
        if (delBtn) {
            delBtn.onclick = async (e) => {
                e.stopPropagation();
                if (!confirm('Are you sure you want to delete this prompt?')) return;

                delBtn.disabled = true;
                const response = await chrome.runtime.sendMessage({
                    type: 'DELETE_PROMPT',
                    payload: { id: prompt.id }
                });

                if (response.success) {
                    item.remove();
                    if (list.children.length === 0) {
                        list.innerHTML = '<p>No prompts found.</p>';
                    }
                } else {
                    alert('Delete failed: ' + response.error);
                    delBtn.disabled = false;
                }
            };
        }

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

    const btn = msgElement.querySelector('.pm-save-btn');
    const originalText = btn.textContent;
    btn.textContent = '⏳ Saving...';
    btn.disabled = true;

    const text = textElement.innerText;
    // Extract first 5 words for title
    const words = text.trim().split(/\s+/).slice(0, 5).join(' ');
    const title = words + (text.split(/\s+/).length > 5 ? '...' : '');

    const response = await chrome.runtime.sendMessage({
        type: 'SAVE_PROMPT',
        payload: {
            text,
            title,
            category_name: 'ChatGPT',
            tag_names: ['ChatGPT']
        }
    });

    if (response.error) {
        alert('Error: ' + response.error);
        btn.textContent = originalText;
        btn.disabled = false;
    } else {
        btn.textContent = '✅ Saved!';
        btn.classList.add('pm-saved');
        setTimeout(() => {
            btn.textContent = originalText;
            btn.classList.remove('pm-saved');
            btn.disabled = false;
        }, 2000);
    }
}

setInterval(addSaveButtons, 2000);
