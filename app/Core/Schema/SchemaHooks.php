<?php

declare(strict_types=1);

namespace App\Core\Schema;

/**
 * Canonical hook names for the Schema Engine.
 *
 * These are integration points only — the engine fires them so future modules
 * and plugins can observe or transform the content lifecycle. The engine ships
 * no business logic on any of these hooks.
 */
final class SchemaHooks
{
    public const string COLLECTION_CREATED = 'khf.schema.collection.created';

    public const string BLUEPRINT_CREATED = 'khf.schema.blueprint.created';

    public const string FIELD_CREATED = 'khf.schema.field.created';

    public const string ENTRY_CREATING = 'khf.schema.entry.creating';

    public const string ENTRY_CREATED = 'khf.schema.entry.created';

    public const string ENTRY_UPDATING = 'khf.schema.entry.updating';

    public const string ENTRY_UPDATED = 'khf.schema.entry.updated';

    public const string ENTRY_PUBLISHED = 'khf.schema.entry.published';

    public const string ENTRY_ARCHIVED = 'khf.schema.entry.archived';

    private function __construct() {}
}
