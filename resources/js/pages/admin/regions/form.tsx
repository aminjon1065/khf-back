import { Head, Link, useForm } from '@inertiajs/react';
import { ArrowLeft, Save } from 'lucide-react';
import { TranslatableField } from '@/components/admin/translatable-field';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';

type LocaleMap = Record<string, string>;

type RegionData = {
    id: number;
    name: LocaleMap;
    center: LocaleMap;
    note: LocaleMap;
    slug: string;
    risk: string;
    active_incidents: number;
    stations: number;
    sort_order: number;
};

type Props = {
    locales: string[];
    risks: { value: string; label: string }[];
    item?: RegionData;
};

export default function RegionForm({ locales, risks, item }: Props) {
    const isEdit = Boolean(item);
    const blank = Object.fromEntries(locales.map((l) => [l, ''])) as LocaleMap;

    const form = useForm({
        name: item?.name ?? { ...blank },
        center: item?.center ?? { ...blank },
        note: item?.note ?? { ...blank },
        slug: item?.slug ?? '',
        risk: item?.risk ?? 'low',
        active_incidents: item?.active_incidents ?? 0,
        stations: item?.stations ?? 0,
        sort_order: item?.sort_order ?? 0,
    });

    const errors = form.errors as Record<string, string>;

    const submit = (event: React.FormEvent) => {
        event.preventDefault();
        if (isEdit && item) {
            form.transform((data) => ({ ...data, _method: 'put' }));
            form.post(`/admin/regions/${item.id}`, { forceFormData: true });
        } else {
            form.transform((data) => data);
            form.post('/admin/regions', { forceFormData: true });
        }
    };

    return (
        <>
            <Head title={isEdit ? 'Редактирование региона' : 'Новый регион'} />

            <form onSubmit={submit}>
                <div className="flex items-center justify-between gap-4">
                    <div className="flex items-center gap-3">
                        <Button asChild variant="ghost" size="icon">
                            <Link href="/admin/regions" aria-label="Назад">
                                <ArrowLeft className="size-4" />
                            </Link>
                        </Button>
                        <h1 className="text-2xl font-bold tracking-tight">
                            {isEdit ? 'Редактирование региона' : 'Новый регион'}
                        </h1>
                    </div>
                    <Button type="submit" disabled={form.processing}>
                        <Save className="size-4" /> Сохранить
                    </Button>
                </div>

                <div className="mt-6 max-w-3xl space-y-5">
                    <TranslatableField
                        name="name"
                        label="Название"
                        locales={locales}
                        value={form.data.name}
                        onChange={(value) => form.setData('name', value)}
                        required
                        errors={errors}
                        as="input"
                        placeholder="Название региона"
                    />

                    <TranslatableField
                        name="center"
                        label="Центр"
                        locales={locales}
                        value={form.data.center}
                        onChange={(value) => form.setData('center', value)}
                        errors={errors}
                        as="input"
                        placeholder="Административный центр"
                    />

                    <TranslatableField
                        name="note"
                        label="Примечание"
                        locales={locales}
                        value={form.data.note}
                        onChange={(value) => form.setData('note', value)}
                        errors={errors}
                        as="textarea"
                        placeholder="Дополнительная информация"
                    />

                    <div className="grid gap-2">
                        <Label>
                            Слаг (URL) <span className="text-destructive">*</span>
                        </Label>
                        <Input
                            value={form.data.slug}
                            onChange={(event) => form.setData('slug', event.target.value)}
                            placeholder="slug-regiona"
                        />
                        <InputError message={errors.slug} />
                    </div>

                    <div className="grid gap-2">
                        <Label>Уровень риска</Label>
                        <Select value={form.data.risk} onValueChange={(value) => form.setData('risk', value)}>
                            <SelectTrigger>
                                <SelectValue />
                            </SelectTrigger>
                            <SelectContent>
                                {risks.map((risk) => (
                                    <SelectItem key={risk.value} value={risk.value}>
                                        {risk.label}
                                    </SelectItem>
                                ))}
                            </SelectContent>
                        </Select>
                        <InputError message={errors.risk} />
                    </div>

                    <div className="grid gap-2">
                        <Label>Активные инциденты</Label>
                        <Input
                            type="number"
                            min={0}
                            value={form.data.active_incidents}
                            onChange={(event) => form.setData('active_incidents', Number(event.target.value))}
                        />
                        <InputError message={errors.active_incidents} />
                    </div>

                    <div className="grid gap-2">
                        <Label>Станции</Label>
                        <Input
                            type="number"
                            min={0}
                            value={form.data.stations}
                            onChange={(event) => form.setData('stations', Number(event.target.value))}
                        />
                        <InputError message={errors.stations} />
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
