/*
 * This file is part of the Symfony project "eliashaeussler/typo3-badges".
 *
 * Copyright (C) 2022 Elias Häußler <elias@haeussler.dev>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

import ClipboardJS from 'clipboard';

/**
 * Clipboard.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
export default class Clipboard {
  defaults = {
    copyIcon: '.clipboard-copy',
    checkIcon: '.clipboard-check',
    timeout: 5000,
    copyText: 'Copy to clipboard',
    successText: 'Copied to clipboard!',
  };

  options;

  buttons;

  clipboard;

  timeouts = [];

  constructor(element, options) {
    this.options = { ...this.defaults, ...options };
    this.buttons = document.querySelectorAll(element);
    this.clipboard = new ClipboardJS(this.buttons, {
      target: (trigger) => trigger.nextElementSibling,
    });

    this.resetAllButtons();

    this.clipboard.on('success', (e) => this.onSuccessfulCopy(e.trigger));
    this.clipboard.on('error', () => this.resetAllButtons());
  }

  /**
   * Perform additional actions on successful copy to clipboard.
   *
   * @param button {Element} The copy button element
   */
  onSuccessfulCopy(button) {
    this.resetAllButtons(false);
    this.hideCopyIcon(button);
    this.timeouts.push(
      window.setTimeout(() => this.resetButton(button), this.options.timeout),
    );
  }

  /**
   * Hide clipboard copy icon after successful copy.
   *
   * @param button {Element} The copy button element
   */
  hideCopyIcon(button) {
    this.getCopyIcon(button).classList.add('hidden');
    this.getCheckIcon(button).classList.remove('hidden');

    button.setAttribute('title', this.options.successText);
  }

  /**
   * Reset copy to clipboard button after successful copy.
   *
   * @param button {Element} The copy button element
   * @param resetFocus {boolean} `true` if focus of button should be lost, `false` otherwise
   */
  resetButton(button, resetFocus = true) {
    this.getCopyIcon(button).classList.remove('hidden');
    this.getCheckIcon(button).classList.add('hidden');

    button.setAttribute('title', this.options.copyText);

    if (resetFocus) {
      button.blur();
    }
  }

  /**
   * Reset all copy to clipboard buttons.
   */
  resetAllButtons(resetFocus = true) {
    this.timeouts.forEach((id) => window.clearTimeout(id));
    this.timeouts = [];

    [...this.buttons].forEach((button) => this.resetButton(button, resetFocus));
  }

  getCopyIcon(button) {
    return button.querySelector(this.options.copyIcon);
  }

  getCheckIcon(button) {
    return button.querySelector(this.options.checkIcon);
  }
}
