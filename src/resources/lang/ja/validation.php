<?php

return [

    /*
    |--------------------------------------------------------------------------
    | バリデーション言語行
    |--------------------------------------------------------------------------
    |
    | 以下の言語行はバリデタークラスで使用されるデフォルトのエラーメッセージです。
    | これらのルールの一部には複数のバージョンがあり、サイズルールのように
    | 各バージョンに適用するメッセージをここに記述します。
    |
    */

    'accepted'             => ':attribute を承認してください。',
    'active_url'           => ':attribute は有効なURLではありません。',
    'after'                => ':attribute には :date 以降の日付を指定してください。',
    'after_or_equal'       => ':attribute には :date 以降の日付を指定してください。',
    'alpha'                => ':attribute はアルファベットのみがご利用できます。',
    'alpha_dash'           => ':attribute はアルファベット、数字、ダッシュ(-)、下線(_)がご利用できます。',
    'alpha_num'            => ':attribute はアルファベットと数字がご利用できます。',
    'array'                => ':attribute は配列でなくてはなりません。',
    'before'               => ':attribute には :date 以前の日付を指定してください。',
    'before_or_equal'      => ':attribute には :date 以前の日付を指定してください。',
    'between'              => [
        'numeric' => ':attribute は :min から :max までの数字で指定してください。',
        'file'    => ':attribute は :min KBから :max KBまでのファイルで指定してください。',
        'string'  => ':attribute は :min 文字から :max 文字までで指定してください。',
        'array'   => ':attribute は :min 個から :max 個までで指定してください。',
    ],
    'boolean'              => ':attribute は true または false を指定してください。',
    'confirmed'            => ':attribute が確認欄と一致していません。',
    'date'                 => ':attribute は有効な日付ではありません。',
    'date_format'          => ':attribute の形式が :format と一致していません。',
    'different'            => ':attribute と :other には異なる値を指定してください。',
    'digits'               => ':attribute は :digits 桁で指定してください。',
    'digits_between'       => ':attribute は :min 桁から :max 桁までで指定してください。',
    'email'                => ':attribute には有効なメールアドレスを指定してください。',
    'exists'               => '選択された :attribute は正しくありません。',
    'file'                 => ':attribute はファイルでなければなりません。',
    'filled'               => ':attribute は値を指定してください。',
    'image'                => ':attribute は画像でなければなりません。',
    'in'                   => '選択された :attribute は正しくありません。',
    'integer'              => ':attribute は整数で指定してください。',
    'ip'                   => ':attribute は有効なIPアドレスを指定してください。',
    'json'                 => ':attribute は有効なJSON文字列でなければなりません。',
    'max'                  => [
        'numeric' => ':attribute は :max 以下で指定してください。',
        'file'    => ':attribute は :max KB以下のファイルで指定してください。',
        'string'  => ':attribute は :max 文字以下で指定してください。',
        'array'   => ':attribute は :max 個以下で指定してください。',
    ],
    'min'                  => [
        'numeric' => ':attribute は :min 以上で指定してください。',
        'file'    => ':attribute は :min KB以上のファイルで指定してください。',
        'string'  => ':attribute は :min 文字以上で入力してください。',
        'array'   => ':attribute は :min 個以上で指定してください。',
    ],
    'not_in'               => '選択された :attribute は正しくありません。',
    'numeric'              => ':attribute は数字で指定してください。',
    'present'              => ':attribute は存在している必要があります。',
    'regex'                => ':attribute の形式が正しくありません。',
    'required'             => ':attribute を入力してください。',
    'required_if'          => ':other が :value の場合、:attribute は必須です。',
    'required_unless'      => ':other が :values でない場合、:attribute は必須です。',
    'required_with'        => ':values が指定されている場合、:attribute は必須です。',
    'required_with_all'    => ':values が全て指定されている場合、:attribute は必須です。',
    'required_without'     => ':values が指定されていない場合、:attribute は必須です。',
    'required_without_all' => ':values が全て指定されていない場合、:attribute は必須です。',
    'same'                 => ':attribute と :other は同じ値を指定してください。',
    'size'                 => [
        'numeric' => ':attribute は :size を指定してください。',
        'file'    => ':attribute は :size KBのファイルを指定してください。',
        'string'  => ':attribute は :size 文字で指定してください。',
        'array'   => ':attribute は :size 個で指定してください。',
    ],
    'string'               => ':attribute は文字列で指定してください。',
    'timezone'             => ':attribute には有効なタイムゾーンを指定してください。',
    'unique'               => ':attribute は既に使用されています。',
    'url'                  => ':attribute の形式が正しくありません。',

    /*
    |--------------------------------------------------------------------------
    | カスタムバリデーション属性
    |--------------------------------------------------------------------------
    |
    | 以下の属性配列は、例えば "email" を "メールアドレス" のように
    | より読みやすい表現に置き換えるために使用されます。
    |
    */

    'attributes' => [
        'name' => '名前',
        'email' => 'メールアドレス',
        'password' => 'パスワード',
        'password_confirmation' => 'パスワード確認',
    ],

];
