import { useState } from 'react';
import { LocaleTabs } from '@/components/admin/locale-tabs';
import InputError from '@/components/input-error';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';

type LocaleMap = Record<string, string>;

/**
 * Переводимое поле со встроенными вкладками локалей (tg/ru/en).
 * value — карта {locale: string}; ошибки берутся из form.errors по ключу `${name}.${locale}`.
 */
export function TranslatableField({
    name,
    label,
    locales,
    value,
    onChange,
    as = 'input',
    required = false,
    placeholder,
    rows = 3,
    errors,
}: {
    name: string;
    label: string;
    locales: string[];
    value: LocaleMap;
    onChange: (value: LocaleMap) => void;
    as?: 'input' | 'textarea';
    required?: boolean;
    placeholder?: string;
    rows?: number;
    errors?: Record<string, string>;
}) {
    const [active, setActive] = useState(locales.includes('ru') ? 'ru' : locales[0]);
    const update = (text: string) => onChange({ ...value, [active]: text });

    return (
        <div className="grid gap-2">
            <div className="flex items-center justify-between gap-3">
                <Label>
                    {label} {required && active === 'ru' && <span className="text-destructive">*</span>}
                </Label>
                <LocaleTabs locales={locales} active={active} onChange={setActive} />
            </div>

            {as === 'textarea' ? (
                <Textarea
                    rows={rows}
                    value={value[active] ?? ''}
                    onChange={(event) => update(event.target.value)}
                    placeholder={placeholder}
                />
            ) : (
                <Input
                    value={value[active] ?? ''}
                    onChange={(event) => update(event.target.value)}
                    placeholder={placeholder}
                />
            )}

            {errors?.[`${name}.${active}`] && <InputError message={errors[`${name}.${active}`]} />}
        </div>
    );
}
