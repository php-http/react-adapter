# Change Log

## [Unreleased]

### Fixed

- Issue with `react/http-client` v0.4.13 about body handling as StreamInterface.
This change was introduce in https://github.com/reactphp/http-client/pull/66.

### Changed

- Client now require a Stream factory to handle body properly.


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
