# Pop

Change software versions for Continuous Integration.

## Warning

This is a demonstration project only. It is not ready to be used in production.

## Background

CI providers (Travis, Circle, etc.) provide tools for chaning software versions.

Here's an example from SemaphoreCI which sets the php version:

```bash
sem-version php 8.2
php -v
```

Pop aims to provide similar tools, that you can install and use anywhere:

```bash
./ci version php 8.2
./ci php -v
```

## Why?

I wanted an unusual project to demonstrate that I know how to:

1. Demonstrate a solution to unusual problems.
2. Use Inheritance, Polymorphism and Interface Segregation.
3. Build something usable in about 4 hours time.
4. Write PSR 4 and PSR 12 compatible code.
5. Improve code using static analysis tools like Psalm.
6. (Hopefully) write a decent README :).

It doesn't demonstrate that I know how to:

1. Write unit tests.
2. Publish stuff on Packagist for all to use.

That's because of the current size and I have [other](https://github.com/WillSkates/Translator) (older) [examples](https://github.com/WillSkates/Quizzes) for those.

## Installation

### Prerequisites

You will need these installed:

- [podman](https://podman.io/) (and set up to use 'Rootless' containers).
- [curl](https://curl.se/)
- [gpg](https://gnupg.org/)

### How

To install, just clone and run the build script:

```bash
git clone https://github.com/WillSkates/pop.git
cd pop
bash ./build
```

## Usage

```bash
./ci version php 8.2
./ci php -v

./ci version php 8.3
./ci php -v
```

## License

This project is published under the MIT license.

Full details can be found in the "LICENSE" file.
