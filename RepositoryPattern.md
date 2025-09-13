# Why We Use the Repository Pattern for CDR Persistence

## Overview
The Repository (or DAO) pattern is a design approach that separates the logic that retrieves and stores data from the business logic. In this project, we use a `CDRRepository` class to handle all database operations for CDR objects.

> **Reference:** For more details, see the [Data Access Object (DAO) pattern on Wikipedia](https://en.wikipedia.org/wiki/Data_access_object).

## Why This Pattern?
- **Separation of Concerns:** Keeps database logic out of the `CDR` model, making code easier to maintain and test. This aligns with the DAO pattern's goal of separating business logic from data access mechanisms, allowing each to evolve independently.
- **Testability:** Repository classes can be easily mocked or stubbed in unit tests, since the data access interface is abstracted from its implementation.
- **Scalability:** As the application grows, complex queries and batch operations can be added to the repository without cluttering the model. The DAO pattern supports this by allowing new data access strategies or implementations to be introduced with minimal impact on business logic.
- **Reusability:** Centralizes all CDR-related database operations, making them reusable across the codebase. This is consistent with the DAO pattern's approach of providing a generic client interface for data access, regardless of the underlying data source or mechanism.
- **Flexibility:** The repository (DAO) pattern allows changes to the data access mechanism (such as switching databases or using a different storage technology) with little or no impact on the code that uses the repository, as long as the interface remains consistent.

> **Reference:** [Benefits of DAO] (https://www.oracle.com/java/technologies/data-access-object.html)

## How It Works
- The `CDRRepository` class provides methods like `save($cdr)` and `findById($uniqueId)`.
- The `CDR` class is a simple data model with no database logic.
- The repository uses the `ConnectionManager` to interact with the database.

## Example Usage
```php
$cdr = new CDR(/* ... */);
$repo = new CDRRepository();
$repo->save($cdr);
```

## Alternatives Considered
- **Instance/static methods on `CDR`:** Mixes data and persistence logic, making code harder to maintain and scale. Consider: What happens if you add additional data persistence methods in the future?
- **Active Record:** Convenient, but can lead to large, hard-to-maintain models.
- **Service Layer:** Useful for complex workflows, but overkill for simple CRUD.

## Conclusion
The repository pattern offers a clean, maintainable, and scalable way to manage CDR persistence, especially as the project grows.
