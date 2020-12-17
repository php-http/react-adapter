# Change Log


All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).


## [Unreleased]

### Changed

- Work with HTTPlug 2, drop HTTPlug 1 support
- Move to `react/http` library instead of `react/http-client`

## [2.3.0] - 2019-07-30

### Changed

- Allow installation with react dns 1.0
- Drop unmaintained PHP version support

## [2.2.0] - 2018-11-03

### Fixed

- React HTTP 0.5 BC break


## [2.1.0] - 2017-12-21

### Changed

- Added compatibility with `react/http-client` v0.5 (compatibility with v0.4 kept)
- `ReactFactory::buildHttpClient` now accepts a `\React\Socket\ConnectorInterface` (only for `react/http-client` v0.5).
If none provided, will use React HTTP Client defaults.

### Deprecations
- Passing a `\React\Dns\Resolver\Resolver` to `ReactFactory::buildHttpClient` is deprecated and will be removed in **3.0.0**.
To control connector behavior (DNS, timeout, etc), pass a `\React\Socket\ConnectorInterface` instead.


## [2.0.0] - 2017-09-18

### Changed

- Promise adapter is internal and not extendable [BC Break]

### Fixed

- Promise adapter rewrote to handle chain operation


## [1.0.0] - 2017-07-08

### Changed

- Tests update to last version


## [0.3.0] - 2016-11-07

### Changed

- Client now require a Stream factory to handle body properly.

### Fixed

- Issue with `react/http-client` v0.4.13 about body handling as StreamInterface.
This change was introduce in https://github.com/reactphp/http-client/pull/66.


## [0.2.2] - 2016-07-18

### Changed

- Client now requires a Response factory instead of a Message factory


## [0.2.1] - 2016-07-18

### Changed

- Updated discovery dependency


## [0.2.0] - 2016-06-28

### Changed

- Updated discovery dependency


## [0.1.1] - 2016-03-08

### Fixed

- Incorrect variable assignment causing impossible to pass custom client and loop


## 0.1.0 - 2016-03-02

- Initial release


[Unreleased]: https://github.com/php-http/react-adapter/compare/v2.2.0...HEAD
[2.2.0]: https://github.com/php-http/react-adapter/compare/2.1.0...v2.2.0
[2.1.0]: https://github.com/php-http/react-adapter/compare/v2.0.0...2.1.0
[2.0.0]: https://github.com/php-http/react-adapter/compare/1.0.0...v2.0.0
[1.0.0]: https://github.com/php-http/react-adapter/compare/v0.3.0...1.0.0
[0.3.0]: https://github.com/php-http/react-adapter/compare/v0.2.2...v0.3.0
[0.2.2]: https://github.com/php-http/react-adapter/compare/v0.2.1...v0.2.2
[0.2.1]: https://github.com/php-http/react-adapter/compare/v0.2.0...v0.2.1
[0.2.0]: https://github.com/php-http/react-adapter/compare/v0.1.1...v0.2.0
[0.1.1]: https://github.com/php-http/react-adapter/compare/v0.1.0...v0.1.1
