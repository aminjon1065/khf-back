<?php

declare(strict_types=1);

namespace App\Core\Actions\Schema;

use App\Core\Actions\Action;
use App\Core\Contracts\HookManagerInterface;
use App\Core\Contracts\Schema\BlueprintRepositoryInterface;
use App\Core\Contracts\Schema\FieldTypeRegistryInterface;
use App\Core\DTO\Schema\CreateBlueprintFieldData;
use App\Core\Models\BlueprintField;
use App\Core\Schema\SchemaHooks;

/**
 * Add a field definition to a blueprint. The field type must be registered;
 * any optional members of the DTO are resolved from the type's defaults before
 * persistence, keeping the catalogue the single source of field-type behaviour.
 */
final class CreateBlueprintFieldAction extends Action
{
    public function __construct(
        private readonly BlueprintRepositoryInterface $blueprints,
        private readonly FieldTypeRegistryInterface $fieldTypes,
        private readonly HookManagerInterface $hooks,
    ) {}

    public function handle(CreateBlueprintFieldData $data): BlueprintField
    {
        $fieldType = $this->fieldTypes->get($data->type);

        $field = $this->blueprints->addField($data->withResolvedDefaults($fieldType));

        $this->hooks->doAction(SchemaHooks::FIELD_CREATED, $field);

        return $field;
    }
}
