# Contributing to CDiscount Octopia SDK

Thank you for your interest in contributing to the CDiscount Octopia SDK!

## How to Contribute

### Reporting Bugs

1. Check if the bug has already been reported in [Issues](../../issues)
2. If not, create a new issue with:
   - Clear title and description
   - Steps to reproduce
   - Expected vs actual behavior
   - PHP version and environment details

### Suggesting Features

1. Open an issue with the `enhancement` label
2. Describe the feature and its use case
3. Provide examples if possible

### Pull Requests

1. Fork the repository
2. Create a feature branch: `git checkout -b feature/my-feature`
3. Make your changes
4. Write/update tests if applicable
5. Ensure code follows PSR-12 coding standards
6. Commit with clear messages: `git commit -m "Add: new feature description"`
7. Push to your fork: `git push origin feature/my-feature`
8. Create a Pull Request

## Development Setup

```bash
# Clone your fork
git clone https://github.com/YOUR_USERNAME/cdiscount-octopia-sdk.git
cd cdiscount-octopia-sdk

# Install dependencies
composer install

# Copy config file
cp config.example.json config.json
# Edit config.json with your credentials
```

## Coding Standards

- Follow PSR-12 coding standards
- Use PHP 7.4 compatible syntax
- Add PHPDoc blocks to all public methods
- Keep methods focused and single-purpose
- Write descriptive variable and method names

## Commit Message Format

- `Add:` for new features
- `Fix:` for bug fixes
- `Update:` for updates to existing features
- `Remove:` for removed features
- `Docs:` for documentation changes
- `Refactor:` for code refactoring

## Questions?

Feel free to open an issue for any questions about contributing.
