<?php

namespace App\Dto\ControllerRequest;

use JMS\Serializer\Annotation;
use App\Component\Interface\AbstractDtoControllerRequest;

class ProductRequest extends AbstractDtoControllerRequest
{
    /**
     * Стоимость товара
     *
     * @var float
     * @Annotation\Type("float")
     * @Annotation\SerializedName("price")
     */
    public float $price;

    /**
     * Наименование товара
     *
     * @var string
     * @Annotation\Type("string")
     * @Annotation\SerializedName("title")
     */
    public string $title;

    /**
     * Описание товара
     *
     * @var string|null
     * @Annotation\Type("string")
     * @Annotation\SerializedName("description")
     */
    public ?string $description = null;

    /**
     * Автор
     *
     * @var string|null
     * @Annotation\Type("string")
     * @Annotation\SerializedName("author")
     */
    public ?string $author = null;

    /**
     * Ссылка на изображение товара
     *
     * @var string|null
     * @Annotation\Type("string")
     * @Annotation\SerializedName("image")
     */
    public ?string $image = null;

    /**
     * Ссылка для скачивания
     *
     * @var string
     * @Annotation\Type("string")
     * @Annotation\SerializedName("url")
     */
    public string $url;
}
