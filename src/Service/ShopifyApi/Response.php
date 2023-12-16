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
        $this->setData($data);
    }

    /**
     * Set Data
     *
     * @param array<mixed> $data
     * @return void
     */
    public function setData(array $data): void
    {
        if (isset($data['extensions'])) {
            unset($data['extensions']);
        }

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
     * Accepts a separated path to the target element. Returns null if not found.
     *
     * @param string $path
     * @param string $separator
     * @return ?string
     */
    public function getStringDataByPath(string $path, string $separator = '/'): ?string
    {
        $data = $this->getMixedDataByPath($path, $separator);
        return is_string($data) ? $data : null;
    }

    /**
     * Accepts a separated path to the target element. Returns null if not found.
     *
     * @param string $path
     * @param string $separator
     * @return ?Response
     */
    public function getResponseDataByPath(string $path, string $separator = '/'): ?Response
    {
        $data = $this->getMixedDataByPath($path, $separator);
        return is_array($data) ? new Response($data) : null;
    }

    /**
     * Accepts a separated path to the target element. Returns null if not found.
     *
     * @param string $path
     * @param string $separator
     * @return mixed
     */
    public function getMixedDataByPath(string $path, string $separator = '/'): mixed
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

        return $data;
    }
}
