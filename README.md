<div align="center">

![Logo](docs/header.png)

# Badges for TYPO3 extensions

[![Coverage](https://sonarcloud.io/api/project_badges/measure?project=eliashaeussler_typo3-badges&metric=coverage)](https://sonarcloud.io/dashboard?id=eliashaeussler_typo3-badges)
[![Deploy](https://github.com/eliashaeussler/typo3-badges/actions/workflows/deploy.yaml/badge.svg)](https://github.com/eliashaeussler/typo3-badges/actions/workflows/deploy.yaml)
[![Tests](https://github.com/eliashaeussler/typo3-badges/actions/workflows/tests.yaml/badge.svg)](https://github.com/eliashaeussler/typo3-badges/actions/workflows/tests.yaml)
[![CGL](https://github.com/eliashaeussler/typo3-badges/actions/workflows/cgl.yaml/badge.svg)](https://github.com/eliashaeussler/typo3-badges/actions/workflows/cgl.yaml)
[![License](http://poser.pugx.org/eliashaeussler/typo3-badges/license)](LICENSE)
[![TYPO3](https://shields.io/endpoint?url=https://badges.typo3-web.dev/badge/typo3)](https://badges.typo3-web.dev)

**:computer:&nbsp;[Official website](https://badges.typo3-web.dev)** |
:package:&nbsp;[Packagist](https://packagist.org/packages/eliashaeussler/typo3-badges) |
:floppy_disk:&nbsp;[Repository](https://github.com/eliashaeussler/typo3-badges) |
:bug:&nbsp;[Issue tracker](https://github.com/eliashaeussler/typo3-badges/issues)

</div>

A Symfony project that provides endpoints for beautiful TYPO3 badges. Pimp all your
extension documentation with badges for extension versions or TER downloads. All
endpoints provide JSON configuration for use with
[Shields.io](https://shields.io/endpoint).

## :zap: Usage

Base URL: `https://badges.typo3-web.dev`

> **Note:**
>
> All endpoints return only the JSON configuration for
> [Shields.io](https://shields.io/endpoint). The actual badges are still rendered via
> Shields.io: `https://shields.io/endpoint?url=<endpoint>`

### Badge for current extension version

Endpoint: `/badge/{extension}/version`

**Example (Markdown):**

```markdown
![Current version](https://shields.io/endpoint?url=https://badges.typo3-web.dev/badge/handlebars/version)
```

**Result:**

![Current version](https://shields.io/endpoint?url=https://badges.typo3-web.dev/badge/handlebars/version)

### Badge for total extension downloads

Endpoint: `/badge/{extension}/downloads`

**Example (Markdown):**

```markdown
![TER downloads](https://shields.io/endpoint?url=https://badges.typo3-web.dev/badge/handlebars/downloads)
```

**Result:**

![TER downloads](https://shields.io/endpoint?url=https://badges.typo3-web.dev/badge/handlebars/downloads)

### Badge for extension key

Endpoint: `/badge/{extension}/extension`

**Example (Markdown):**

```markdown
![TYPO3 extension](https://shields.io/endpoint?url=https://badges.typo3-web.dev/badge/handlebars/extension)
```

**Result:**

![TYPO3 extension](https://shields.io/endpoint?url=https://badges.typo3-web.dev/badge/handlebars/extension)

### Badge for extension stability

Endpoint: `/badge/{extension}/stability`

**Example (Markdown):**

```markdown
![Stability](https://shields.io/endpoint?url=https://badges.typo3-web.dev/badge/handlebars/stability)
```

**Result:**

![Stability](https://shields.io/endpoint?url=https://badges.typo3-web.dev/badge/handlebars/stability)

### Generic TYPO3 badge

Endpoint: `/badge/typo3`

**Example (Markdown):**

```markdown
![TYPO3](https://shields.io/endpoint?url=https://badges.typo3-web.dev/badge/typo3)
```

**Result:**

![TYPO3](https://shields.io/endpoint?url=https://badges.typo3-web.dev/badge/typo3)

## :star: License

This project is licensed under [GNU General Public License 3.0 (or later)](LICENSE).
