# Shopify App Bundle
## Summary
This bundle provides a base installable Shopify app.

## Required ENV Variables
### SHOPIFY_APP_URL_KEY
The App's URL key. This is used to generate redirects to the app in the Shopify Admin.

Usage: `config/services.yml`
### SHOPIFY_API_KEY
Shopify App API Key

Usage: `config/services.yml`
### SHOPIFY_API_SECRET
Shopify App API Secret

Usage: `config/services.yml`
### SHOPIFY_SCOPES
Shopify App API Scopes

Example: `read_products,write_inventory`

Usage: `config/services.yml`

### SHOPIFY_HOST_NAME
Hostname of the app including a schema, used by the Shopify API for callbacks.

Example: `https://example.com`

Usage: `config/services.yml`