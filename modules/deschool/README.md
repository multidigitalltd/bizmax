# DeSchool module (bundled)

Theme-bundled copy of the **DeSchool** plugin (`md-deschool` 1.17.0, Multi Digital).

- `bootstrap.php` replaces the plugin's `md-deschool.php`: same constants (`MDDS_*`), same autoloader,
  same `MultiDigital\DeSchool\mdds()` accessor. It is loaded by `inc/deschool.php` only when the
  standalone plugin is **not** active.
- `includes/`, `templates/`, `assets/`, `languages/` are the plugin's files, unchanged, except
  `Plugin::load_textdomain()`, which loads an optional `.mo` from `languages/` when bundled.
- `MDDS_VERSION` is `1.17.0-bizmax-{theme version}`, so asset URLs and the one-time rewrite flush
  follow theme releases.

Data contracts (post types, meta keys, user meta, options, shortcodes, AJAX actions, endpoints,
filters) are identical to the plugin; see the theme README, section "DeSchool".
