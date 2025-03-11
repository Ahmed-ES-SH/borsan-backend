<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMessageRequest extends FormRequest
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
            'conversation_id' => 'required|exists:conversations,id',
            'sender_id' => 'required|exists:users,id',
            'message' => 'nullable|string',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,mp3,pdf,docx|max:40960',
        ];
    }



    public function messages(): array
    {
        return [
            'conversation_id.required' => ['ar' => 'معرف المحادثة مطلوب.', 'en' => 'Conversation ID is required.'],
            'conversation_id.exists' => ['ar' => 'المحادثة غير موجودة.', 'en' => 'The conversation does not exist.'],

            'sender_id.required' => ['ar' => 'معرف المرسل مطلوب.', 'en' => 'Sender ID is required.'],
            'sender_id.exists' => ['ar' => 'المستخدم غير موجود.', 'en' => 'The sender does not exist.'],

            'message.string' => ['ar' => 'يجب أن يكون النص من نوع سلسلة.', 'en' => 'The message must be a string.'],

            'attachment.file' => ['ar' => 'يجب أن يكون الملف مرفقًا صالحًا.', 'en' => 'The attachment must be a valid file.'],
            'attachment.mimes' => [
                'ar' => 'يجب أن يكون الملف بصيغة: jpg, jpeg, png, mp3, pdf, docx.',
                'en' => 'The attachment must be a file of type: jpg, jpeg, png, mp3, pdf, docx.'
            ],
            'attachment.max' => ['ar' => 'حجم الملف لا يمكن أن يتجاوز 40 ميجابايت.', 'en' => 'The attachment may not be greater than 40MB.']
        ];
    }
}
