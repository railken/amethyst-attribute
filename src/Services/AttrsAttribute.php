<?php

namespace Railken\Amethyst\Services;

use Closure;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Railken\Bag;
use Railken\Lem\Concerns;
use Railken\Lem\Contracts\AttributeContract;
use Railken\Lem\Contracts\EntityContract;
use Railken\Lem\Exceptions as Exceptions;
use Railken\Lem\Tokens;
use Respect\Validation\Validator as v;
use Illuminate\Support\Facades\Cache;
use Railken\Lem\Attributes\BaseAttribute;
use Railken\Amethyst\Managers\AttributeValueManager;

class AttrsAttribute extends BaseAttribute
{

    /**
     * Is the attribute fillable.
     *
     * @var bool
     */
    protected $fillable = true;

    /**
     * Update entity value.
     *
     * @param \Railken\Lem\Contracts\EntityContract $entity
     * @param \Railken\Bag                          $parameters
     *
     * @return Collection
     */
    public function fill(EntityContract $entity, Bag $parameters)
    {
        $errors = new Collection();
        if ($parameters->exists($this->getName())) {

            $entity->internalAttributes->set($this->getName(), $this->parse($parameters->get($this->getName())));
        }

        return $errors;
    }

    /**
     * Parse value.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    public function parse($value)
    {
        return AttributeBag::factory($value);
    }

    /**
     * Save attribute.
     *
     * @param \Railken\Lem\Contracts\EntityContract $entity
     *
     * @return \Illuminate\Support\Collection;
     */
    public function save(EntityContract $entity)
    {
    	$manager = new AttributeValueManager();

    	$errors = Collection::make();

        foreach ($entity->internalAttributes->get($this->getName(), []) as $key => $value) {
        	$errors = $errors->merge($manager->updateOrCreate([
	            'attributable_type' => $entity->getMorphName(),
	            'attributable_id'   => $entity->id,
	            'attribute_id'		=> app('amethyst.attributable')->findAttributeByName($key)->id,
	        ], [
	        	'value'             => $value
	        ])->getErrors());
		}

		return $errors;
    }

    /**
     * Push readable
     *
     * @param \Railken\Lem\Contracts\EntityContract $entity
     * @param \Railken\Bag                          $parameters
     *
     * @return $parameters
     */
    public function pushReadable(EntityContract $entity, Bag $parameters)
    {
        $parameters->set($this->getName(), $entity->internalAttributes->get($this->getName(), new Bag())->toArray());

        return $parameters;
    }   
}