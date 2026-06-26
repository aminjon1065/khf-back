import { Head, Link, useForm } from '@inertiajs/react';
import { ArrowLeft, Save } from 'lucide-react';
import { TranslatableField } from '@/components/admin/translatable-field';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

type LocaleMap = Record<string, string>;

type OfficeData = {
    id: number;
    region: LocaleMap;
    address: LocaleMap;
    hours: LocaleMap;
    phone: string | null;
    email: string | null;
    is_head: boolean;
    sort_order: number | null;
};

type Props = {
    locales: string[];
    item?: OfficeData;
};

export default function OfficeForm({ locales, item }: Props) {
    const isEdit = Boolean(item);
    const blank = Object.fromEntries(locales.map((l) => [l, ''])) as LocaleMap;

    const form = useForm({
        region: item?.region ?? { ...blank },
        address: item?.address ?? { ...blank },
        hours: item?.hours ?? { ...blank },
        phone: item?.phone ?? '',
        email: item?.email ?? '',
        is_head: item?.is_head ?? false,
        sort_order: item?.sort_order ?? 0,
    });

    const submit = (event: React.FormEvent) => {
        event.preventDefault();
        if (isEdit && item) {
            form.transform((data) => ({ ...data, _method: 'put' }));
            form.post(`/admin/offices/${item.id}`, { forceFormData: true });
        } else {
            form.transform((data) => data);
            form.post('/admin/offices', { forceFormData: true });
        }
    };

    return (
        <>
            <Head title={isEdit ? 'Редактирование офиса' : 'Новый офис'} />

            <form onSubmit={submit}>
                <div className="flex items-center justify-between gap-4">
                    <div className="flex items-center gap-3">
                        <Button asChild variant="ghost" size="icon">
                            <Link href="/admin/offices" aria-label="Назад">
                                <ArrowLeft className="size-4" />
                            </Link>
                        </Button>
                        <h1 className="text-2xl font-bold tracking-tight">
                            {isEdit ? 'Редактирование офиса' : 'Новый офис'}
                        </h1>
                    </div>
                    <Button type="submit" disabled={form.processing}>
                        <Save className="size-4" /> Сохранить
                    </Button>
                </div>

                <div className="mt-6 max-w-3xl space-y-5">
                    <TranslatableField
                        name="region"
                        label="Регион"
                        locales={locales}
                        value={form.data.region}
                        onChange={(value) => form.setData('region', value)}
                        required
                        errors={form.errors as Record<string, string>}
                        as="input"
                    />

                    <TranslatableField
                        name="address"
                        label="Адрес"
                        locales={locales}
                        value={form.data.address}
                        onChange={(value) => form.setData('address', value)}
                        errors={form.errors as Record<string, string>}
                        as="textarea"
                    />

                    <TranslatableField
                        name="hours"
                        label="Часы работы"
                        locales={locales}
                        value={form.data.hours}
                        onChange={(value) => form.setData('hours', value)}
                        errors={form.errors as Record<string, string>}
                        as="input"
                    />

                    <div className="grid gap-2">
                        <Label>Телефон</Label>
                        <Input
                            value={form.data.phone}
                            onChange={(event) => form.setData('phone', event.target.value)}
                            placeholder="+992 44 600 00 00"
                        />
                        <InputError message={form.errors.phone} />
                    </div>

                    <div className="grid gap-2">
                        <Label>Email</Label>
                        <Input
                            type="email"
                            value={form.data.email}
                            onChange={(event) => form.setData('email', event.target.value)}
                            placeholder="office@khf.tj"
                        />
                        <InputError message={form.errors.email} />
                    </div>

                    <div className="flex items-center gap-2">
                        <Checkbox
                            id="is_head"
                            checked={form.data.is_head}
                            onCheckedChange={(checked) => form.setData('is_head', checked === true)}
                        />
                        <Label htmlFor="is_head">Головной офис</Label>
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
