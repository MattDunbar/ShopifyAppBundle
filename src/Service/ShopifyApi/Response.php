<?php

namespace MattDunbar\ShopifyAppBundle\Service\ShopifyApi;

class Response
{
    /** @var array<mixed> */
    protected array $data;

    /**
     * Constructor
     *
     * @param array<mixed> $data
     */
    public function __construct(
        array $data = []
    ) {
        $this->data = $data;
    }

    /**
     * Set Data
     *
     * @param array<mixed> $data
     * @return void
     */
    public function setData(array $data): void
    {
        if (sizeof($data) == 1 && isset($data['data']) && is_array($data['data'])) {
            $data = $data['data'];
        }
        $this->data = $data;
    }

    /**
     * Get Data
     *
     * @return array<mixed>
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Accepts a / separated path to the target element. Returns null if not found.
     *
     * E.g. 'data/shop/name' returns $this->getData()['data']['shop']['name'
     *
     * @param string $path
     * @param string $separator
     * @return string|null
     */
    public function getStringDataByPath(string $path, string $separator = '/'): ?string
    {
        if ($separator === '') {
            $separator = '/';
        }
        $pathParts = explode($separator, $path);
        $data = $this->getData();
        foreach ($pathParts as $pathPart) {
            if (!is_array($data) || !isset($data[$pathPart])) {
                return null;
            }
            $data = $data[$pathPart];
        }

        if (is_array($data)) {
            return null;
        }

        return (string) $data;
    }
}
