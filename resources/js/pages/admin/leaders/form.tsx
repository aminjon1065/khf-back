import { Head, Link, useForm } from '@inertiajs/react';
import { ArrowLeft, Save } from 'lucide-react';
import { CoverUploader, type CoverUrls } from '@/components/admin/cover-uploader';
import { TranslatableField } from '@/components/admin/translatable-field';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

type LocaleMap = Record<string, string>;

type LeaderData = {
    id: number;
    name: LocaleMap;
    role: LocaleMap;
    rank: LocaleMap;
    bio: LocaleMap;
    sort_order: number;
    photo: CoverUrls | null;
};

type Props = {
    locales: string[];
    item?: LeaderData;
};

export default function LeaderForm({ locales, item }: Props) {
    const isEdit = Boolean(item);
    const blank = Object.fromEntries(locales.map((l) => [l, ''])) as LocaleMap;

    const form = useForm({
        name: item?.name ?? { ...blank },
        role: item?.role ?? { ...blank },
        rank: item?.rank ?? { ...blank },
        bio: item?.bio ?? { ...blank },
        sort_order: item?.sort_order ?? 0,
        photo: null as File | null,
    });

    const errors = form.errors as Record<string, string>;

    const submit = (event: React.FormEvent) => {
        event.preventDefault();
        if (isEdit && item) {
            form.transform((data) => ({ ...data, _method: 'put' }));
            form.post(`/admin/leaders/${item.id}`, { forceFormData: true });
        } else {
            form.transform((data) => data);
            form.post('/admin/leaders', { forceFormData: true });
        }
    };

    return (
        <>
            <Head title={isEdit ? 'Редактирование руководителя' : 'Новый руководитель'} />

            <form onSubmit={submit}>
                <div className="flex items-center justify-between gap-4">
                    <div className="flex items-center gap-3">
                        <Button asChild variant="ghost" size="icon">
                            <Link href="/admin/leaders" aria-label="Назад">
                                <ArrowLeft className="size-4" />
                            </Link>
                        </Button>
                        <h1 className="text-2xl font-bold tracking-tight">
                            {isEdit ? 'Редактирование руководителя' : 'Новый руководитель'}
                        </h1>
                    </div>
                    <Button type="submit" disabled={form.processing}>
                        <Save className="size-4" /> Сохранить
                    </Button>
                </div>

                <div className="mt-6 max-w-3xl space-y-5">
                    <TranslatableField
                        name="name"
                        label="Имя"
                        locales={locales}
                        value={form.data.name}
                        onChange={(v) => form.setData('name', v)}
                        required
                        as="input"
                        errors={errors}
                    />

                    <TranslatableField
                        name="role"
                        label="Должность"
                        locales={locales}
                        value={form.data.role}
                        onChange={(v) => form.setData('role', v)}
                        required
                        as="input"
                        errors={errors}
                    />

                    <TranslatableField
                        name="rank"
                        label="Звание"
                        locales={locales}
                        value={form.data.rank}
                        onChange={(v) => form.setData('rank', v)}
                        as="input"
                        errors={errors}
                    />

                    <TranslatableField
                        name="bio"
                        label="Биография"
                        locales={locales}
                        value={form.data.bio}
                        onChange={(v) => form.setData('bio', v)}
                        as="textarea"
                        errors={errors}
                    />

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
                        <Label>Фото</Label>
                        <CoverUploader
                            existing={item?.photo ?? null}
                            onChange={(file) => form.setData('photo', file)}
                            aspectRatio={1}
                        />
                        <InputError message={errors.photo} />
                    </div>
                </div>
            </form>
        </>
    );
}
