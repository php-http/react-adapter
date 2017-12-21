# Change Log

## 2.1.0 - 2017-12-21

### Changed

- Added compatibility with `react/http-client` v0.5 (compatibility with v0.4 kept)
- `ReactFactory::buildHttpClient` now accepts a `\React\Socket\ConnectorInterface` (only for `react/http-client` v0.5).
If none provided, will use React HTTP Client defaults. 

### Deprecations
- Passing a `\React\Dns\Resolver\Resolver` to `ReactFactory::buildHttpClient` is deprecated and will be removed in **3.0.0**.
To control connector behavior (DNS, timeout, etc), pass a `\React\Socket\ConnectorInterface` instead.

## 2.0.0 - 2017-09-18

### Changed

- Promise adapter is internal and not extendable [BC Break]

### Fixed

- Promise adapter rewrote to handle chain operation

## 1.0.0 - 2017-07-08

### Changed

- Tests update to last version

## 0.3.0 - 2016-11-07

### Changed

- Client now require a Stream factory to handle body properly.

### Fixed

- Issue with `react/http-client` v0.4.13 about body handling as StreamInterface.
This change was introduce in https://github.com/reactphp/http-client/pull/66.


## 0.2.2 - 2016-07-18

### Changed

- Client now requires a Response factory instead of a Message factory


## 0.2.1 - 2016-07-18

### Changed

- Updated discovery dependency


## 0.2.0 - 2016-06-28

### Changed

- Updated discovery dependency


## 0.1.1 - 2016-03-08

### Fixed

- Incorrect variable assignment causing impossible to pass custom client and loop


## 0.1.0 - 2016-03-02

- Initial release
