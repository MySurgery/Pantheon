# Custom Pantheon upstream for WordPress

WordPress core is imported via composer; this is handled in an external CI process before the resulting configuration is pushed to Github.

*Notes:*
- This upstream uses a sub-directory for the root (`/web`)
- There a a number of core files removed for security.
