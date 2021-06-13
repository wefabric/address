<?php


namespace Wefabric\Address\Validation;


use App\Validation\WishPeopleValidation;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class PostcodeAPIValidation implements Validator
{
    protected $data = [];

    /**
     * @var bool
     */
    protected $validated = true;

    /**
     * @var array
     */
    protected $errorBag = [];

    /**
     * WishPeopleValidation constructor.
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @param array $data
     * @return WishPeopleValidation
     */
    public static function make(array $data) {
        return new self($data);
    }

    /**
     * @return bool
     * @throws ValidationException
     */
    public function validate()
    {
        $this->validated = false;
        $this->errorBag['postcode_api'] = __('wefabric_address::address.postcode_housenumber_required');
        if(isset($this->data['postcode'], $this->data['housenumber']) && $this->data['postcode'] && $this->data['housenumber']) {
            $this->validated = true;
            unset($this->errorBag['postcode_api']);
        }

        if(!$this->validated()) {
            throw new ValidationException($this);
        }

        return true;
    }

    public function getMessageBag()
    {
        return $this->errorBag;
    }

    public function validated()
    {
        return $this->validated;
    }

    public function fails()
    {
        return $this->validated ? false : true;
    }

    public function failed()
    {
        return $this->fails();
    }

    public function sometimes($attribute, $rules, callable $callback)
    {
        // TODO: Implement sometimes() method.
    }

    public function after($callback)
    {
        // TODO: Implement after() method.
    }

    public function errors()
    {
        return $this->errorBag;
    }
}
