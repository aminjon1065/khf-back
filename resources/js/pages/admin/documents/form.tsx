import { Head, Link, useForm } from '@inertiajs/react';
import { ArrowLeft, Save } from 'lucide-react';
import { TranslatableField } from '@/components/admin/translatable-field';
import { FileUploader } from '@/components/admin/file-uploader';
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

type DocumentData = {
    id: number;
    title: LocaleMap;
    number: string | null;
    document_date: string | null;
    size: string | null;
    sort_order: number | null;
    type: string | null;
    document_category_id: number | null;
    file_name: string | null;
    file_url: string | null;
};

type Props = {
    locales: string[];
    categories: { id: number; name: string }[];
    types: { value: string; label: string }[];
    item?: DocumentData;
};

export default function DocumentForm({ locales, categories, types, item }: Props) {
    const isEdit = Boolean(item);
    const blank = Object.fromEntries(locales.map((l) => [l, ''])) as LocaleMap;

    const form = useForm({
        title: item?.title ?? { ...blank },
        number: item?.number ?? '',
        document_date: item?.document_date ?? '',
        size: item?.size ?? '',
        sort_order: item?.sort_order ?? 0,
        type: item?.type ?? null,
        document_category_id: item?.document_category_id ?? null,
        file: null as File | null,
    });

    const errors = form.errors as Record<string, string>;

    const submit = (event: React.FormEvent) => {
        event.preventDefault();
        if (isEdit && item) {
            form.transform((data) => ({ ...data, _method: 'put' }));
            form.post(`/admin/documents/${item.id}`, { forceFormData: true });
        } else {
            form.transform((data) => data);
            form.post('/admin/documents', { forceFormData: true });
        }
    };

    return (
        <>
            <Head title={isEdit ? 'Редактирование документа' : 'Новый документ'} />

            <form onSubmit={submit}>
                <div className="flex items-center justify-between gap-4">
                    <div className="flex items-center gap-3">
                        <Button asChild variant="ghost" size="icon">
                            <Link href="/admin/documents" aria-label="Назад">
                                <ArrowLeft className="size-4" />
                            </Link>
                        </Button>
                        <h1 className="text-2xl font-bold tracking-tight">
                            {isEdit ? 'Редактирование документа' : 'Новый документ'}
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
                        placeholder="Название документа"
                    />

                    <div className="grid gap-2">
                        <Label>Категория</Label>
                        <Select
                            value={form.data.document_category_id ? String(form.data.document_category_id) : 'none'}
                            onValueChange={(value) =>
                                form.setData('document_category_id', value === 'none' ? null : Number(value))
                            }
                        >
                            <SelectTrigger>
                                <SelectValue placeholder="Без категории" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="none">Без категории</SelectItem>
                                {categories.map((category) => (
                                    <SelectItem key={category.id} value={String(category.id)}>
                                        {category.name}
                                    </SelectItem>
                                ))}
                            </SelectContent>
                        </Select>
                    </div>

                    <div className="grid gap-2">
                        <Label>Тип</Label>
                        <Select
                            value={form.data.type ?? 'none'}
                            onValueChange={(value) => form.setData('type', value === 'none' ? null : value)}
                        >
                            <SelectTrigger>
                                <SelectValue placeholder="Не указан" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="none">Не указан</SelectItem>
                                {types.map((type) => (
                                    <SelectItem key={type.value} value={type.value}>
                                        {type.label}
                                    </SelectItem>
                                ))}
                            </SelectContent>
                        </Select>
                    </div>

                    <div className="grid gap-2">
                        <Label>Номер</Label>
                        <Input
                            value={form.data.number}
                            onChange={(event) => form.setData('number', event.target.value)}
                            placeholder="№ 123"
                        />
                        <InputError message={errors.number} />
                    </div>

                    <div className="grid gap-2">
                        <Label>Дата документа</Label>
                        <Input
                            type="date"
                            value={form.data.document_date}
                            onChange={(event) => form.setData('document_date', event.target.value)}
                        />
                        <InputError message={errors.document_date} />
                    </div>

                    <div className="grid gap-2">
                        <Label>Размер</Label>
                        <Input
                            value={form.data.size}
                            onChange={(event) => form.setData('size', event.target.value)}
                            placeholder="1.2 МБ"
                        />
                        <InputError message={errors.size} />
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

                    <div className="grid gap-2">
                        <Label>Файл</Label>
                        <FileUploader
                            existingName={item?.file_name ?? null}
                            existingUrl={item?.file_url ?? null}
                            onChange={(file) => form.setData('file', file)}
                        />
                        <InputError message={errors.file} />
                    </div>
                </div>
            </form>
        </>
    );
}
