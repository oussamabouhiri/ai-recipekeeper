## Context

The AI Recipe Keeper application is a new Laravel 12 project with only the default authentication scaffolding in place. The database schema needs to be created from scratch based on validated MCD and MLD diagrams. The existing users table follows Laravel's default structure.

## Goals / Non-Goals

**Goals:**
- Create a complete database schema supporting all recipe management features
- Implement Laravel 12 migrations with proper foreign keys and indexes
- Create Eloquent models with defined relationships
- Follow Laravel naming conventions and best practices

**Non-Goals:**
- Implement controllers, routes, or business logic
- Create Blade views or frontend components
- Implement authentication logic beyond schema structure
- Add seeders or factories (can be done separately)

## Decisions

### Decision: Use Laravel naming conventions
**Choice**: Follow Laravel's camelCase for columns and snake_case for tables
**Rationale**: Consistency with Laravel ecosystem, easier integration with Eloquent
**Alternatives**: Use custom naming conventions (rejected for maintainability)

### Decision: Implement cascade deletes appropriately
**Choice**: Use cascade deletes for owned relationships (user→recipes, recipe→steps)
**Rationale**: Data integrity when parent records are deleted
**Alternatives**: Soft deletes (considered but not required by specs)

### Decision: Use pivot tables for many-to-many relationships
**Choice**: Create separate pivot tables for recipe-ingredient and recipe-category
**Rationale**: Standard Laravel approach, allows additional columns on relationships
**Alternatives**: Use composite keys (less flexible)

### Decision: Index foreign keys and frequently queried columns
**Choice**: Add indexes on all foreign keys and commonly filtered columns
**Rationale**: Query performance optimization
**Alternatives**: Skip indexes (rejected for production readiness)

## Risks / Trade-offs

**Risk**: Schema changes after implementation → Mitigation: Follow validated MCD/MLD strictly
**Risk**: Performance with complex queries → Mitigation: Proper indexing strategy
**Risk**: Migration conflicts → Mitigation: Use timestamp-based migration naming
