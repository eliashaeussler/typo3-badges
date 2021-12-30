import ClipboardJS from 'clipboard';

const timeouts = {};

/**
 * Hide clipboard copy icon after successful copy.
 *
 * @param button {Element} The copy button element
 */
const hideCopyIcon = (button) => {
  const copyIcon = button.querySelector('.clipboard-copy');
  const checkIcon = button.querySelector('.clipboard-check');

  copyIcon.classList.add('hidden');
  checkIcon.classList.remove('hidden');

  button.setAttribute('title', 'Copied to clipboard!');
};

/**
 * Reset copy to clipboard button after successful copy.
 *
 * @param button {Element} The copy button element
 */
const resetClipboardButton = (button) => {
  const copyIcon = button.querySelector('.clipboard-copy');
  const checkIcon = button.querySelector('.clipboard-check');

  copyIcon.classList.remove('hidden');
  checkIcon.classList.add('hidden');

  button.setAttribute('title', 'Copy to clipboard');
};

const clipboard = new ClipboardJS('.clipboard-btn', {
  target: (trigger) => trigger.nextElementSibling,
});

clipboard.on('success', (e) => {
  hideCopyIcon(e.trigger);

  if (e.trigger in timeouts) {
    window.clearTimeout(timeouts[e.trigger]);
  }

  timeouts[e.trigger] = window.setTimeout(() => resetClipboardButton(e.trigger), 5000);
});
