---
title: Exploring command options
weight: 4
---

The `transform-urls` command processes all registered URL transformations and stores the results in your database.

Run all registered transformations:

```bash
php artisan transform-urls
```

The command provides several options to control which transformations are processed and how they run.

### Filtering by URL

Transform only specific URLs using exact matches or wildcards:

```bash
# Transform a specific URL
php artisan transform-urls --url="https://spatie.be/blog"

# Use wildcards to transform multiple URLs
php artisan transform-urls --url="https://spatie.be/*"

# Transform all blog posts
php artisan transform-urls --url="*/blog/*"
```

### Filtering by transformer

Process only specific transformers:

```bash
# Transform only with the ldJson transformer
php artisan transform-urls --transformer="ldJson"

# Use wildcards for transformer names
php artisan transform-urls --transformer="image*"
```

### Force transformations

By default, transformers can skip processing using the `shouldRun()` method. Force them to run regardless:

```bash
php artisan transform-urls --force
```

This is useful when:
- Testing transformers that have conditional logic
- Re-processing content that was previously skipped
- Overriding transformer conditions temporarily

### Synchronous execution

By default, transformations are queued for background processing. Run them immediately instead:

```bash
php artisan transform-urls --now
```
