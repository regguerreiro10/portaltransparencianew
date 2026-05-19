<?php

class BComboNoResultsService
{
    public static function getQuickFieldValue($param)
    {
        if(!empty($param['_field_data_json']))
        {
            $data = json_decode($param['_field_data_json']);
            return $data->quick_register_value;
        }

        return '';
    }

    public static function getProperties($param)
    {
        if(!empty($param['_field_data_json']))
        {
            $data = json_decode($param['_field_data_json']);

            if(!empty($data->noresultsbtnprops))
            {
                $props = unserialize(base64_decode(Crypt::decryptString($data->noresultsbtnprops)));
            
                return $props;
            }
        }

        if(!empty($param['form_noresultsbtnprops']))
        {
            $data = json_decode($param['form_noresultsbtnprops']);

            $props = unserialize(base64_decode(Crypt::decryptString($data->noresultsbtnprops)));
            
            return $props;
        }

        if(!empty($param['noresultsbtnprops']))
        {
            $data = json_decode($param['noresultsbtnprops']);

            $props = unserialize(base64_decode(Crypt::decryptString($data->noresultsbtnprops)));
            
            return $props;
        }

        return false;
    }

    public static function getPropertiesJson($param)
    {
        if(!empty($param['noresultsbtnprops']))
        {
            return $param['noresultsbtnprops'];
        }

        if(!empty($param['_field_data_json']))
        {
            return $param['_field_data_json'];
        }
    
        return false;
    }

    /**
     * Handles the refresh of component in screen with new record data
     * 
     * @param array $param form data containing component information
     * @param object $object Newly created record
     * @return void
     */
    public static function handleRefreshComponent($param, $object)
    {
        $props = self::getProperties($param);

        if($props && in_array($props->component, ['TDBCombo', 'TCombo']))
        {
            TCombo::addOption($props->field_form, $props->field_name, $object->{$props->key}, $object->render($props->column));
            TForm::sendData($props->field_form, (object) [$props->field_name => $object->{$props->key} ], false, false);
        }
        elseif($props && in_array($props->component, ['TDBUniqueSearch']))
        {
            TForm::sendData($props->field_form, (object) [$props->field_name => $object->{$props->key} ], false, false);
        }
    }

    public static function handleRefreshComponentProduto($param, $object)
    {
        $props = self::getProperties($param);
        if (!$props) {
            return;
        }

        $field_name_raw = $props->field_name ?? '';                 // ex: campo[]
        $base_field     = preg_replace('/\[\]$/', '', $field_name_raw); // ex: campo
        $field_id       = $props->field_id ?? '';                   // ex: campo_1950853 (select da linha)
        $newKey         = $object->{$props->key};
        $newLabel       = $object->render($props->column);

        // Detecta FieldList (no request vem como array no nome base)
        $isFieldList = is_array($param[$base_field] ?? null);

        if ($props && in_array($props->component, ['TDBCombo', 'TCombo']))
        {
            // 1) garante opção no backend (sem [])
            TCombo::addOption($props->field_form, $base_field, $newKey, $newLabel);

            // 2) Se for FieldList: NÃO usar sendData em campo[] (clona) e sim JS no PARENT pelo field_id
            if ($isFieldList)
            {
                $val   = (int) $newKey;
                $label = addslashes($newLabel);

                // força executar no formulário PAI (parent/top). E seleciona só o select dessa linha.
                TScript::create("
                    (function(){
                        var w = window.parent;
                        if (window.top && window.top.document) w = window.top; // fallback mais forte

                        if (!w || !w.jQuery) { console.log('jQuery não encontrado no parent/top'); return; }

                        var \$sel = w.jQuery('#{$field_id}');
                        if (!\$sel.length) { console.log('Select não encontrado no parent: {$field_id}'); return; }

                        if (\$sel.find('option[value=\"{$val}\"]').length === 0) {
                            \$sel.append(new w.Option('{$label}', '{$val}', true, true));
                        }

                        \$sel.val('{$val}').trigger('change');
                    })();
                ");

                return;
            }

            // Campo normal (fora do FieldList)
            TForm::sendData($props->field_form, (object) [$base_field => $newKey], false, false);
            return;
        }

        if ($props && in_array($props->component, ['TDBUniqueSearch']))
        {
            // UniqueSearch normal
            TForm::sendData($props->field_form, (object) [$base_field => $newKey], false, false);
            return;
        }
    }



    public static function refreshFieldListCombo($param, $object)
    {
        $props = self::getProperties($param);
        if (!$props) {
            throw new Exception('Não foi possível ler noresultsbtnprops.');
        }

        // ID do SELECT da LINHA (ex: itens_..._produto_id_1950853)
        $fieldId = $props->field_id ?? null;
        if (!$fieldId) {
            throw new Exception('field_id não veio no noresultsbtnprops.');
        }

        $val   = (int) $object->{$props->key};                  // novo ID
        $label = addslashes($object->render($props->column));   // label do combo

        // (opcional, mas bom) garante opção no backend (sem [])
        $baseField = preg_replace('/\[\]$/', '', $props->field_name ?? '');
        if ($baseField) {
            TCombo::addOption($props->field_form, $baseField, $val, $label);
        }

        // Atualiza o select DA LINHA usando o field_id no DOM do PAI
        TScript::create("
        (function(){
            var w = window, guard = 0;
            while (w && !w.jQuery && w.parent && w !== w.parent && guard++ < 10) w = w.parent;
            if (window.top && window.top.jQuery) w = window.top;
            if (!w || !w.jQuery) { console.log('Sem jQuery no parent/top'); return; }

            var \$sel = w.jQuery('#{$fieldId}', w.document);
            if (!\$sel.length) { console.log('Select não encontrado: {$fieldId}'); return; }

            // adiciona option se não existir
            if (\$sel.find('option[value=\"{$val}\"]').length === 0) {
                \$sel.append(new w.Option('{$label}', '{$val}', true, true));
            }

            // seta valor e atualiza select2
            \$sel.val('{$val}').trigger('change');
            \$sel.trigger('change.select2');
        })();
        ");
    }


    

}