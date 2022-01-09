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

/**
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
describe('Clipboard Test', { browser: 'electron' }, () => {
  beforeEach(() => {
    cy.visit('/');

    cy.get('details[open] > .badge-endpoint-example:not(.hidden) .group')
      .as('container');

    cy.get('@container')
      .find('.clipboard-btn')
      .as('button');
  });

  it('hides clipboard button by default', () => {
    cy.get('@button')
      .should('not.be.visible');
  });

  it('makes clipboard button visible on hovering parent', () => {
    cy.get('@container')
      .realHover();

    cy.get('@button')
      .should('be.visible');
  });

  it('should always make clipboard button visible on touch devices', () => {
    cy.get('html')
      .then(($el) => {
        $el.addClass('touch');
        $el.removeClass('no-touch');
      });

    cy.get('@button')
      .should('be.visible');
  });

  it('copies text to the clipboard', () => {
    let clipboardText;

    cy.get('@container')
      .realHover();

    cy.get('@button')
      .click()
      .should('be.focused');

    cy.window()
      .then((window) => {
        window.navigator.clipboard
          .readText()
          .then((text) => {
            clipboardText = text;
          });
      });

    cy.get('@container')
      .should(($subject) => {
        expect($subject.text().trim()).to.eq(clipboardText);
      });
  });

  it('restores clipboard button 2 seconds after copy', () => {
    cy.get('@container')
      .realHover();

    cy.clock();

    cy.get('@button')
      .click();

    cy.tick(2000);

    cy.get('@button')
      .should('not.be.focused');
  });
});
