document.addEventListener('DOMContentLoaded', () => {
    const loadingOverlay = document.querySelector('[data-admin-loading]');
    const loadingMessage = loadingOverlay?.querySelector('[data-admin-loading-message]');
    const loadingTargets = document.querySelectorAll('.admin-sidebar, .admin-sidebar-backdrop, .admin-main');
    let previousFocus = null;

    const setAdminLoading = (isLoading, message = '処理中です。しばらくお待ちください。') => {
        if (!loadingOverlay || !loadingMessage) return;

        if (isLoading) {
            previousFocus = document.activeElement;
            loadingMessage.textContent = message;
            loadingOverlay.hidden = false;
            loadingTargets.forEach((target) => target.inert = true);
            document.body.classList.add('admin-is-loading');
            loadingOverlay.focus();
            return;
        }

        loadingOverlay.hidden = true;
        loadingTargets.forEach((target) => target.inert = false);
        document.body.classList.remove('admin-is-loading');
        if (previousFocus instanceof HTMLElement && previousFocus.isConnected) {
            previousFocus.focus();
        }
        previousFocus = null;
    };

    document.querySelectorAll('[data-searchable-select]').forEach((select) => {
        const component = document.createElement('div');
        const trigger = document.createElement('button');
        const panel = document.createElement('div');
        const search = document.createElement('input');
        const list = document.createElement('div');
        const empty = document.createElement('p');
        const label = document.querySelector(`label[for="${select.id}"]`);
        const options = Array.from(select.options);

        select.classList.add('admin-searchable-select-native');
        component.className = 'admin-searchable-select';
        trigger.className = 'admin-form-control admin-searchable-select-trigger';
        trigger.type = 'button';
        trigger.setAttribute('aria-haspopup', 'listbox');
        trigger.setAttribute('aria-expanded', 'false');
        trigger.setAttribute('aria-controls', `${select.id}-list`);
        if (label) trigger.setAttribute('aria-labelledby', label.id || `${label.htmlFor}-label`);
        if (label && !label.id) label.id = `${label.htmlFor}-label`;

        panel.className = 'admin-searchable-select-panel';
        panel.hidden = true;
        search.className = 'admin-searchable-select-search';
        search.type = 'search';
        search.placeholder = select.dataset.searchPlaceholder || '検索';
        search.setAttribute('aria-label', search.placeholder);
        list.className = 'admin-searchable-select-list';
        list.id = `${select.id}-list`;
        list.setAttribute('role', 'listbox');
        empty.className = 'admin-searchable-select-empty';
        empty.textContent = '該当する市町村がありません。';
        empty.hidden = true;
        panel.append(search, list, empty);
        component.append(trigger, panel);
        select.insertAdjacentElement('afterend', component);

        const close = () => {
            panel.hidden = true;
            trigger.setAttribute('aria-expanded', 'false');
            search.value = '';
            renderOptions();
        };
        const open = () => {
            panel.hidden = false;
            trigger.setAttribute('aria-expanded', 'true');
            search.focus();
        };
        const updateTrigger = () => {
            trigger.textContent = select.selectedOptions[0]?.textContent.trim() || '選択してください';
        };
        const renderOptions = () => {
            const query = search.value.trim().toLocaleLowerCase();
            list.replaceChildren();

            options.forEach((option) => {
                if (!option.textContent.toLocaleLowerCase().includes(query)) return;

                const item = document.createElement('button');
                item.className = 'admin-searchable-select-option';
                item.type = 'button';
                item.textContent = option.textContent;
                item.setAttribute('role', 'option');
                item.setAttribute('aria-selected', String(option.selected));
                item.addEventListener('click', () => {
                    select.value = option.value;
                    select.dispatchEvent(new Event('change', { bubbles: true }));
                    updateTrigger();
                    close();
                    trigger.focus();
                });
                list.append(item);
            });

            empty.hidden = list.childElementCount > 0;
        };

        trigger.addEventListener('click', () => panel.hidden ? open() : close());
        trigger.addEventListener('keydown', (event) => {
            if (event.key === 'ArrowDown' || event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                open();
            }
        });
        search.addEventListener('input', renderOptions);
        search.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                event.preventDefault();
                close();
                trigger.focus();
            } else if (event.key === 'ArrowDown') {
                event.preventDefault();
                list.querySelector('button')?.focus();
            } else if (event.key === 'Enter') {
                event.preventDefault();
                list.querySelector('button')?.click();
            }
        });
        panel.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                close();
                trigger.focus();
            }
        });
        list.addEventListener('keydown', (event) => {
            if (!['ArrowDown', 'ArrowUp'].includes(event.key)) return;

            event.preventDefault();
            const buttons = Array.from(list.querySelectorAll('button'));
            const currentIndex = buttons.indexOf(document.activeElement);
            const offset = event.key === 'ArrowDown' ? 1 : -1;
            buttons[(currentIndex + offset + buttons.length) % buttons.length]?.focus();
        });
        document.addEventListener('click', (event) => {
            if (!component.contains(event.target)) close();
        });
        select.form?.addEventListener('reset', () => window.requestAnimationFrame(updateTrigger));

        updateTrigger();
        renderOptions();
    });

    const selectAllStories = document.querySelector('[data-select-all-stories]');
    if (selectAllStories) {
        const storyCheckboxes = Array.from(document.querySelectorAll('[data-story-checkbox]'));
        selectAllStories.addEventListener('change', () => {
            storyCheckboxes.forEach((checkbox) => {
                checkbox.checked = selectAllStories.checked;
            });
        });
        storyCheckboxes.forEach((checkbox) => {
            checkbox.addEventListener('change', () => {
                selectAllStories.checked = storyCheckboxes.length > 0
                    && storyCheckboxes.every((item) => item.checked);
            });
        });
    }

    const bulkTagsDialog = document.querySelector('[data-bulk-tags-dialog]');
    const openBulkTags = document.querySelector('[data-open-bulk-tags]');
    if (bulkTagsDialog && openBulkTags) {
        const storyCheckboxes = Array.from(document.querySelectorAll('[data-story-checkbox]'));
        const selectedCount = bulkTagsDialog.querySelector('[data-bulk-selected-count]');

        openBulkTags.addEventListener('click', () => {
            const count = storyCheckboxes.filter((checkbox) => checkbox.checked).length;
            if (count === 0) {
                window.alert('タグを付ける民話を選択してください。');
                return;
            }
            selectedCount.textContent = `選択した民話：${count}件`;
            bulkTagsDialog.showModal();
        });
        bulkTagsDialog.querySelectorAll('[data-close-bulk-tags]').forEach((button) => {
            button.addEventListener('click', () => bulkTagsDialog.close());
        });
        bulkTagsDialog.addEventListener('click', (event) => {
            if (event.target === bulkTagsDialog) bulkTagsDialog.close();
        });
    }

    const storyUploadForm = document.querySelector('[data-story-upload-form]');
    storyUploadForm?.addEventListener('submit', () => {
        const hasImageUpload = Array.from(storyUploadForm.querySelectorAll('input[type="file"]'))
            .some((input) => input.files?.length > 0);

        if (hasImageUpload) {
            setAdminLoading(true, '画像をアップロードしています。しばらくお待ちください。');
        }
    });

    const sidebar = document.querySelector('[data-admin-sidebar]');
    const sidebarToggle = document.querySelector('[data-admin-sidebar-toggle]');
    const sidebarBackdrop = document.querySelector('[data-admin-sidebar-backdrop]');

    if (sidebar && sidebarToggle) {
        const closeSidebar = () => {
            sidebar.classList.remove('is-open');
            sidebarBackdrop?.classList.remove('is-open');
            sidebarToggle.setAttribute('aria-expanded', 'false');
        };

        sidebarToggle.addEventListener('click', () => {
            const isOpen = sidebar.classList.toggle('is-open');
            sidebarBackdrop?.classList.toggle('is-open', isOpen);
            sidebarToggle.setAttribute('aria-expanded', String(isOpen));
        });

        sidebarBackdrop?.addEventListener('click', closeSidebar);
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 768) closeSidebar();
        });
    }

    const body = document.querySelector('#story-body');
    if (!body) return;

    document.querySelectorAll('[data-font-size]').forEach((button) => {
        button.addEventListener('click', () => {
            const size = button.dataset.fontSize;
            body.classList.remove('text-small', 'text-medium', 'text-large');
            body.classList.add(`text-${size}`);

            document.querySelectorAll('[data-font-size]').forEach((item) => item.classList.remove('active'));
            button.classList.add('active');
        });
    });
});
