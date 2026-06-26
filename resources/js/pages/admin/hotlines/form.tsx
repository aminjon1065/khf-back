import { Head, Link, useForm } from '@inertiajs/react';
import { ArrowLeft, Save } from 'lucide-react';
import { TranslatableField } from '@/components/admin/translatable-field';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

type LocaleMap = Record<string, string>;

type HotlineData = {
    id: number;
    number: string;
    label: LocaleMap;
    note: LocaleMap;
    is_primary: boolean;
    sort_order: number | null;
};

type Props = {
    locales: string[];
    item?: HotlineData;
};

export default function HotlineForm({ locales, item }: Props) {
    const isEdit = Boolean(item);
    const blank = Object.fromEntries(locales.map((l) => [l, ''])) as LocaleMap;

    const form = useForm({
        number: item?.number ?? '',
        label: item?.label ?? { ...blank },
        note: item?.note ?? { ...blank },
        is_primary: item?.is_primary ?? false,
        sort_order: item?.sort_order ?? 0,
    });

    const submit = (event: React.FormEvent) => {
        event.preventDefault();
        if (isEdit && item) {
            form.transform((data) => ({ ...data, _method: 'put' }));
            form.post(`/admin/hotlines/${item.id}`, { forceFormData: true });
        } else {
            form.transform((data) => data);
            form.post('/admin/hotlines', { forceFormData: true });
        }
    };

    return (
        <>
            <Head title={isEdit ? 'Редактирование горячей линии' : 'Новая горячая линия'} />

            <form onSubmit={submit}>
                <div className="flex items-center justify-between gap-4">
                    <div className="flex items-center gap-3">
                        <Button asChild variant="ghost" size="icon">
                            <Link href="/admin/hotlines" aria-label="Назад">
                                <ArrowLeft className="size-4" />
                            </Link>
                        </Button>
                        <h1 className="text-2xl font-bold tracking-tight">
                            {isEdit ? 'Редактирование горячей линии' : 'Новая горячая линия'}
                        </h1>
                    </div>
                    <Button type="submit" disabled={form.processing}>
                        <Save className="size-4" /> Сохранить
                    </Button>
                </div>

                <div className="mt-6 max-w-3xl space-y-5">
                    <div className="grid gap-2">
                        <Label>
                            Номер <span className="text-destructive">*</span>
                        </Label>
                        <Input
                            value={form.data.number}
                            onChange={(event) => form.setData('number', event.target.value)}
                            placeholder="+992 44 600 00 00"
                        />
                        <InputError message={form.errors.number} />
                    </div>

                    <TranslatableField
                        name="label"
                        label="Название"
                        locales={locales}
                        value={form.data.label}
                        onChange={(value) => form.setData('label', value)}
                        required
                        errors={form.errors as Record<string, string>}
                        as="input"
                    />

                    <TranslatableField
                        name="note"
                        label="Примечание"
                        locales={locales}
                        value={form.data.note}
                        onChange={(value) => form.setData('note', value)}
                        errors={form.errors as Record<string, string>}
                        as="textarea"
                    />

                    <div className="flex items-center gap-2">
                        <Checkbox
                            id="is_primary"
                            checked={form.data.is_primary}
                            onCheckedChange={(checked) => form.setData('is_primary', checked === true)}
                        />
                        <Label htmlFor="is_primary">Основная</Label>
                    </div>

                    <div className="grid gap-2">
                        <Label>Порядок сортировки</Label>
                        <Input
                            type="number"
                            value={form.data.sort_order}
                            onChange={(event) => form.setData('sort_order', Number(event.target.value))}
                        />
                        <InputError message={form.errors.sort_order} />
                    </div>
                </div>
            </form>
        </>
    );
}
