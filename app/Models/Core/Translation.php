<?php

namespace App\Models\Core;

use App\Http\Controllers\Admin\Profile\ProfileController;
use Illuminate\Database\Eloquent\Model;

class Translation extends Model {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'CORE_TRANSLATION';
    protected $primaryKey = 'ID';
    /**
     * Indicates if the model should be timestamped.    *
     * @var bool
     */
    public $timestamps = false;

    /*
     * Defaults
     */
    protected $defaults = array(
        'ACTIVE' => TRUE,
    );

    public function __construct(array $attributes = array())
    {
        $this->setRawAttributes($this->defaults, true);
        parent::__construct($attributes);
    }

    /**
     * Functie voor standaard ophalen of aanmaken vertaling
     * @param $language
     * @param $transkey
     * @return static
     */
    public static function findOrCreateTranslation($language,$transkey)
    {
        $obj = static::where('ACTIVE',TRUE)->where('FK_CORE_LANGUAGE', $language)->where('FK_CORE_TRANSLATION_KEY', $transkey)->first();
        return $obj ?: new static;
    }

    /**
     * Opslaan van KJ translationfield
     * @param $transkey
     * @param $inputArray
     */
    public static function saveKJTranslationInput($transkey, $inputArray)
    {
        foreach($inputArray as $langid => $value) {
            if ($value != '')
            {
                $translation = static::findOrCreateTranslation($langid, $transkey);
                $translation->FK_CORE_LANGUAGE = $langid;
                $translation->FK_CORE_TRANSLATION_KEY = $transkey;
                $translation->TEXT =  $value;
                $translation->save();
            } else {
                $obj = static::where('ACTIVE',TRUE)->where('FK_CORE_LANGUAGE', $langid)->where('FK_CORE_TRANSLATION_KEY', $transkey)->first();
                if ($obj) {
                    $obj->delete();
                }
            }
        }
    }

    public static function getValue($translation_key, $forceLocaleId = 0)
    {
        $localeId = 0;
        if ($forceLocaleId > 0) {
            $localeId = $forceLocaleId;
        } else {
            $localeId = config('app.locale_id') ? config('app.locale_id') : config('language.defaultLangID');
        }

        // Get in current language
        $translation = Translation::where('FK_CORE_TRANSLATION_KEY', $translation_key)
            ->where('FK_CORE_LANGUAGE', $localeId)
            ->first();

        $translation_value = ($translation->TEXT ?? '');

        // Fallback default language
        if ($translation_value == '') {
            if ($localeId != config('language.defaultLangID')) {
                $translation = Translation::where('FK_CORE_TRANSLATION_KEY', $translation_key)
                    ->where('FK_CORE_LANGUAGE', config('language.defaultLangID'))
                    ->first();

                $translation_value = ($translation->TEXT ?? '');
            }
        }

        // Return value
        return $translation_value;
    }

}