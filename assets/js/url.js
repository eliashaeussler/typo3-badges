/*
 * This file is part of the Symfony project "eliashaeussler/typo3-badges".
 *
 * Copyright (C) 2024 Elias Häußler <elias@haeussler.dev>
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
 * Url.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
export default class Url {
  /**
   * Read given parameter value from url hash.
   *
   * @param name {string} Name of the parameter inside url hash
   * @returns {string} Value of given parameter from url hash
   */
  static getFromHash(name) {
    const searchParams = Url.parseHash();

    return searchParams.get(name);
  }

  /**
   * Store given parameter in url hash.
   *
   * @param name {string} Name of the parameter to store in url hash
   * @param value {string} Value of the parameter to store in url hash
   */
  static setInHash(name, value) {
    const searchParams = Url.parseHash();

    searchParams.set(name, value);

    window.location.hash = searchParams.toString();
  }

  /**
   * Delete given parameter from url hash.
   *
   * @param name {string} Name of the parameter to delete from url hash
   */
  static deleteFromHash(name) {
    const searchParams = Url.parseHash();

    searchParams.delete(name);

    window.location.hash = searchParams.toString();
  }

  /**
   * Parse url hash into URLSearchParams object.
   *
   * @returns {URLSearchParams} Object of parsed parameters from url hash
   */
  static parseHash() {
    let { hash } = window.location;

    if (hash === null || hash.length === 0) {
      return new URLSearchParams();
    }

    if (hash.startsWith('#')) {
      hash = hash.substr(1);
    }

    return new URLSearchParams(hash);
  }
}
