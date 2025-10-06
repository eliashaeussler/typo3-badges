/*
 * This file is part of the Symfony project "eliashaeussler/typo3-badges".
 *
 * Copyright (C) 2023 Elias Häußler <elias@haeussler.dev>
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

import { Modal, Tabs } from 'flowbite';
import BadgeProviderToggle from './provider/badgeProviderToggle';
import Clipboard from '../clipboard';
import CodeTabs from './codeTabs';
import LazyLoad from './lazyLoad';
import Url from '../url';

/**
 * TryOut.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
export default class TryOut {
  defaults = {
    placement: 'center',
    backdrop: 'static',
    backdropClasses: 'bg-gray-900/50 dark:bg-gray-900/80 fixed inset-0 z-40',
    onShow: () => this.showModal(),
    onHide: () => this.hideModal(),
  };

  options;

  triggers;

  element;

  input;

  codeTemplate;

  errorTemplates;

  output;

  extensionKeyInput;

  terLink;

  modal;

  constructor(trigger, options) {
    this.options = { ...this.defaults, ...options };
    this.triggers = document.querySelectorAll(trigger);
    this.element = document.querySelector('#try-out-modal');
    this.input = document.querySelector('#try-out-extension-key');
    this.modal = new Modal(this.element, this.options);

    // Store try-out template sections
    this.readTemplates();

    // Open modal on click on trigger elements
    [...this.triggers].forEach((t) => {
      t.addEventListener('click', () => this.modal.toggle());
    });

    // Close modal on click on modal backdrop
    this.element.addEventListener('click', (e) => {
      if (e.target === this.element) {
        this.modal.toggle();
      }
    });

    // Preselect extension from url
    const preselectedExtension = Url.getFromHash('extension');
    if (preselectedExtension !== null) {
      this.modal.show();
      this.applyTemplate(preselectedExtension);
    }
  }

  static isAvailable() {
    return document.querySelector('#try-out-modal') !== null;
  }

  /**
   * Show try-out modal.
   */
  showModal() {
    this.input.focus();
    this.element.querySelector('form').onsubmit = (e) => {
      e.preventDefault();

      const extensionKey = this.input.value.trim().toLowerCase();
      this.applyTemplate(extensionKey);
    };

    const currentExtensionKey = this.element.dataset.extensionKey ?? '';
    if (currentExtensionKey !== '') {
      Url.setInHash('extension', currentExtensionKey);
    }
  }

  /**
   * Hide try-out modal.
   */
  hideModal() {
    Url.deleteFromHash('extension');
  }

  /**
   * Read and store try-out template section.
   */
  readTemplates() {
    this.codeTemplate = document.querySelector('#try-out-code-template').innerHTML;
    this.errorTemplates = Object.fromEntries(
      Array.from(document.querySelectorAll('#try-out-error-template > [data-error-identifier]')).map((el) => [
        el.dataset.errorIdentifier,
        el.innerHTML,
      ]),
    );
    this.output = document.querySelector('#try-out-output');
    this.extensionKeyInput = document.querySelector('#try-out-extension-key');
    this.terLink = document.querySelector('#try-out-ter-link');
  }

  /**
   * Apply try-out template section with given extension key.
   *
   * @param extensionKey {string} Current extension key
   */
  applyTemplate(extensionKey) {
    this.output.classList.remove('hidden');
    this.extensionKeyInput.value = extensionKey;

    // Fail on Composer package names
    if (extensionKey.includes('/')) {
      this.output.innerHTML = this.errorTemplates['composer-name-detected'].replaceAll('PACKAGE_NAME', extensionKey);
      this.handleError();
      return;
    }

    // Fail on invalid extension key
    if (!extensionKey.match(/^[a-z][a-z0-9_]+$/)) {
      this.output.innerHTML = this.errorTemplates['invalid-extension-key'].replaceAll('PACKAGE_NAME', extensionKey);
      this.handleError();
      return;
    }

    this.element.dataset.extensionKey = extensionKey;
    this.output.innerHTML = this.codeTemplate.replaceAll('EXTENSION_KEY', extensionKey);
    this.terLink.innerHTML = this.terLink.innerHTML.replaceAll('EXTENSION_KEY', extensionKey);
    this.terLink.classList.remove('hidden');

    // Connect badge provider toggles
    const badgeProviderToggle = new BadgeProviderToggle('#try-out-modal .badge-providers-button');

    // Connect clipboard buttons
    Clipboard.connect('.clipboard-btn');

    // Create code tabs
    badgeProviderToggle.getAllProviders().forEach((provider) => {
      new Tabs(
        document.querySelector(`[data-tabs-toggle="#badge-example-${provider}-${extensionKey}-container"]`),
        [
          {
            id: 'md',
            triggerEl: this.element.querySelector(`#badge-example-${provider}-${extensionKey}-md-trigger`),
            targetEl: this.element.querySelector(`#badge-example-${provider}-${extensionKey}-md`),
          },
          {
            id: 'rst',
            triggerEl: this.element.querySelector(`#badge-example-${provider}-${extensionKey}-rst-trigger`),
            targetEl: this.element.querySelector(`#badge-example-${provider}-${extensionKey}-rst`),
          },
          {
            id: 'html',
            triggerEl: this.element.querySelector(`#badge-example-${provider}-${extensionKey}-html-trigger`),
            targetEl: this.element.querySelector(`#badge-example-${provider}-${extensionKey}-html`),
          },
        ],
        {
          defaultTabId: CodeTabs.getCurrentLanguage() ?? 'md',
        },
      );
    });

    // Connect code tabs
    CodeTabs.connect();

    // Connect lazy-loading for badges
    LazyLoad.connect(this.element);

    // Store in URL
    Url.setInHash('extension', extensionKey);
  }

  /**
   * Handle invalid extension key inputs.
   */
  handleError() {
    this.element.dataset.extensionKey = '';
    this.terLink.classList.add('hidden');

    Url.deleteFromHash('extension');
  }
}
