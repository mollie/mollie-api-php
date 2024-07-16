# Upgrading
## From v2 to v3
### Removed unused Collections
This change should not have any impact on your code, but if you have a type hint for any of the following classes, make sure to remove it
- `Mollie\Api\Resources\OrganizationCollection`
- `Mollie\Api\Resources\RouteCollection`


### Removed deprecations
The following was removed due to a deprecation
- `Mollie\Api\Types\OrderStatus::REFUNDED`
- `Mollie\Api\Types\OrderLineStatus::REFUNDED`
