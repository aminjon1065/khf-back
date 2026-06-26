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

type ProgramData = {
    id: number;
    title: LocaleMap;
    description: LocaleMap;
    period: string | null;
    status: string;
    sort_order: number | null;
};

type Props = {
    locales: string[];
    statuses: { value: string; label: string }[];
    item?: ProgramData;
};

export default function ProgramForm({ locales, statuses, item }: Props) {
    const isEdit = Boolean(item);
    const blank = Object.fromEntries(locales.map((l) => [l, ''])) as LocaleMap;

    const form = useForm({
        title: item?.title ?? { ...blank },
        description: item?.description ?? { ...blank },
        period: item?.period ?? '',
        status: item?.status ?? statuses[0]?.value ?? '',
        sort_order: item?.sort_order ?? 0,
    });

    const errors = form.errors as Record<string, string>;

    const submit = (event: React.FormEvent) => {
        event.preventDefault();
        if (isEdit && item) {
            form.transform((data) => ({ ...data, _method: 'put' }));
            form.post(`/admin/programs/${item.id}`, { forceFormData: true });
        } else {
            form.transform((data) => data);
            form.post('/admin/programs', { forceFormData: true });
        }
    };

    return (
        <>
            <Head title={isEdit ? 'Редактирование программы' : 'Новая программа'} />

            <form onSubmit={submit}>
                <div className="flex items-center justify-between gap-4">
                    <div className="flex items-center gap-3">
                        <Button asChild variant="ghost" size="icon">
                            <Link href="/admin/programs" aria-label="Назад">
                                <ArrowLeft className="size-4" />
                            </Link>
                        </Button>
                        <h1 className="text-2xl font-bold tracking-tight">
                            {isEdit ? 'Редактирование программы' : 'Новая программа'}
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
                        onChange={(v) => form.setData('title', v)}
                        required
                        errors={errors}
                        as="input"
                    />

                    <TranslatableField
                        name="description"
                        label="Описание"
                        locales={locales}
                        value={form.data.description}
                        onChange={(v) => form.setData('description', v)}
                        errors={errors}
                        as="textarea"
                    />

                    <div className="grid gap-2">
                        <Label>Период</Label>
                        <Input
                            value={form.data.period}
                            onChange={(event) => form.setData('period', event.target.value)}
                            placeholder="2024–2025"
                        />
                        <InputError message={errors.period} />
                    </div>

                    <div className="grid gap-2">
                        <Label>Статус</Label>
                        <Select value={form.data.status} onValueChange={(value) => form.setData('status', value)}>
                            <SelectTrigger>
                                <SelectValue />
                            </SelectTrigger>
                            <SelectContent>
                                {statuses.map((status) => (
                                    <SelectItem key={status.value} value={status.value}>
                                        {status.label}
                                    </SelectItem>
                                ))}
                            </SelectContent>
                        </Select>
                        <InputError message={errors.status} />
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
