import { Head, Link, useForm } from '@inertiajs/react';
import { ArrowLeft, Save } from 'lucide-react';
import { TranslatableField } from '@/components/admin/translatable-field';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

type LocaleMap = Record<string, string>;

type ServiceData = {
    id: number;
    title: LocaleMap;
    subtitle: LocaleMap;
    key: string | null;
    icon: string | null;
    is_primary: boolean;
    tel: string | null;
    route_key: string | null;
    sort_order: number;
};

type Props = {
    locales: string[];
    item?: ServiceData;
};

export default function ServiceForm({ locales, item }: Props) {
    const isEdit = Boolean(item);
    const blank = Object.fromEntries(locales.map((l) => [l, ''])) as LocaleMap;

    const form = useForm({
        title: item?.title ?? { ...blank },
        subtitle: item?.subtitle ?? { ...blank },
        key: item?.key ?? '',
        icon: item?.icon ?? '',
        is_primary: item?.is_primary ?? false,
        tel: item?.tel ?? '',
        route_key: item?.route_key ?? '',
        sort_order: item?.sort_order ?? 0,
    });

    const errors = form.errors as Record<string, string>;

    const submit = (event: React.FormEvent) => {
        event.preventDefault();
        if (isEdit && item) {
            form.transform((data) => ({ ...data, _method: 'put' }));
            form.post(`/admin/services/${item.id}`, { forceFormData: true });
        } else {
            form.transform((data) => data);
            form.post('/admin/services', { forceFormData: true });
        }
    };

    return (
        <>
            <Head title={isEdit ? 'Редактирование сервиса' : 'Новый сервис'} />

            <form onSubmit={submit}>
                <div className="flex items-center justify-between gap-4">
                    <div className="flex items-center gap-3">
                        <Button asChild variant="ghost" size="icon">
                            <Link href="/admin/services" aria-label="Назад">
                                <ArrowLeft className="size-4" />
                            </Link>
                        </Button>
                        <h1 className="text-2xl font-bold tracking-tight">
                            {isEdit ? 'Редактирование сервиса' : 'Новый сервис'}
                        </h1>
                    </div>
                    <Button type="submit" disabled={form.processing}>
                        <Save className="size-4" /> Сохранить
                    </Button>
                </div>

                <div className="mt-6 max-w-3xl space-y-5">
                    <TranslatableField
                        name="title"
                        label="Название"
                        locales={locales}
                        value={form.data.title}
                        onChange={(value) => form.setData('title', value)}
                        required
                        errors={errors}
                        as="input"
                        placeholder="Название сервиса"
                    />

                    <TranslatableField
                        name="subtitle"
                        label="Подзаголовок"
                        locales={locales}
                        value={form.data.subtitle}
                        onChange={(value) => form.setData('subtitle', value)}
                        errors={errors}
                        as="input"
                        placeholder="Краткое описание"
                    />

                    <div className="grid gap-2">
                        <Label>Ключ</Label>
                        <Input
                            value={form.data.key}
                            onChange={(event) => form.setData('key', event.target.value)}
                            placeholder="service-key"
                        />
                        <InputError message={errors.key} />
                    </div>

                    <div className="grid gap-2">
                        <Label>Иконка</Label>
                        <Input
                            value={form.data.icon}
                            onChange={(event) => form.setData('icon', event.target.value)}
                            placeholder="Например: phone"
                        />
                        <InputError message={errors.icon} />
                    </div>

                    <div className="flex items-center gap-2">
                        <Checkbox
                            id="is_primary"
                            checked={form.data.is_primary}
                            onCheckedChange={(checked) => form.setData('is_primary', checked === true)}
                        />
                        <Label htmlFor="is_primary">Основной сервис</Label>
                    </div>
                    <InputError message={errors.is_primary} />

                    <div className="grid gap-2">
                        <Label>Телефон</Label>
                        <Input
                            value={form.data.tel}
                            onChange={(event) => form.setData('tel', event.target.value)}
                            placeholder="+992 ..."
                        />
                        <InputError message={errors.tel} />
                    </div>

                    <div className="grid gap-2">
                        <Label>Ключ маршрута</Label>
                        <Input
                            value={form.data.route_key}
                            onChange={(event) => form.setData('route_key', event.target.value)}
                            placeholder="route-key"
                        />
                        <InputError message={errors.route_key} />
                    </div>

                    <div className="grid gap-2">
                        <Label>Порядок сортировки</Label>
                        <Input
                            type="number"
                            value={form.data.sort_order}
                            onChange={(event) => form.setData('sort_order', Number(event.target.value))}
                        />
                        <InputError message={errors.sort_order} />
                    </div>
                </div>
            </form>
        </>
    );
}
