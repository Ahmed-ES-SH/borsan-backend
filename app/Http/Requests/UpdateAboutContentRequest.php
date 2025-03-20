<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAboutContentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_section_title_en' => 'string|sometimes',
            'first_section_title_ar' => 'string|sometimes',
            'first_section_contnet_ar'   => 'sometimes|string',
            'first_section_contnet_en'   => 'sometimes|string',
            'second_section_title_en' => 'string|sometimes',
            'second_section_title_ar' => 'string|sometimes',
            'second_section_contnet_ar'         => 'sometimes|string',
            'second_section_contnet_en'         => 'sometimes|string',
            'thired_section_title_en' => 'string|sometimes',
            'thired_section_title_ar' => 'string|sometimes',
            'thired_section_contnet_ar'          => 'sometimes|string',
            'thired_section_contnet_en'          => 'sometimes|string',
            'fourth_section_title_en' => 'string|sometimes',
            'fourth_section_title_ar' => 'string|sometimes',
            'fourth_seaction_contnet_ar'         => 'sometimes|string',
            'fourth_seaction_contnet_en'         => 'sometimes|string',
            'show_map'          => 'sometimes|boolean',
            'address'           => 'nullable|string|max:255',
            'first_section_image'       => 'nullable',
            'second_section_image'      => 'nullable',
            'thired_section_image'       => 'nullable',
            'fourth_section_image'      => 'nullable',
            'main_video'        => 'nullable',
            'link_video'        => 'nullable',
        ];
    }
}
