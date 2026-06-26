import { Head, Link, router } from '@inertiajs/react';
import { Pencil, Plus, Trash2 } from 'lucide-react';
import { Button } from '@/components/ui/button';

type Row = {
    id: number;
    title: string;
    slug: string;
    topics_count: number;
};

export default function ForumCategoriesIndex({ items }: { items: Row[] }) {
    const remove = (row: Row) => {
        if (confirm(`Удалить «${row.title}»?`)) {
            router.delete(`/admin/forum-categories/${row.id}`, { preserveScroll: true });
        }
    };

    return (
        <>
            <Head title="Категории форума" />

            <div className="flex items-center justify-between gap-4">
                <div>
                    <h1 className="text-2xl font-bold tracking-tight">Категории форума</h1>
                    <p className="mt-1 text-sm text-muted-foreground">Разделы форума портала.</p>
                </div>
                <Button asChild>
                    <Link href="/admin/forum-categories/create">
                        <Plus className="size-4" /> Добавить
                    </Link>
                </Button>
            </div>

            <div className="mt-6 overflow-x-auto rounded-xl border">
                <table className="w-full text-sm">
                    <thead className="bg-muted/50 text-left text-xs uppercase tracking-wide text-muted-foreground">
                        <tr>
                            <th className="p-3 font-medium">Название</th>
                            <th className="p-3 font-medium">Слаг</th>
                            <th className="p-3 text-right font-medium">Темы</th>
                            <th className="p-3" />
                        </tr>
                    </thead>
                    <tbody className="divide-y">
                        {items.map((row) => (
                            <tr key={row.id} className="hover:bg-muted/30">
                                <td className="p-3">
                                    <span className="line-clamp-2 max-w-md font-medium">{row.title}</span>
                                </td>
                                <td className="p-3 text-muted-foreground">{row.slug}</td>
                                <td className="p-3 text-right tabular-nums">{row.topics_count.toLocaleString('ru-RU')}</td>
                                <td className="p-3">
                                    <div className="flex justify-end gap-1">
                                        <Button asChild size="icon" variant="ghost">
                                            <Link href={`/admin/forum-categories/${row.id}/edit`} aria-label="Редактировать">
                                                <Pencil className="size-4" />
                                            </Link>
                                        </Button>
                                        <Button size="icon" variant="ghost" onClick={() => remove(row)} aria-label="Удалить">
                                            <Trash2 className="size-4 text-destructive" />
                                        </Button>
                                    </div>
                                </td>
                            </tr>
                        ))}
                        {items.length === 0 && (
                            <tr>
                                <td colSpan={4} className="p-8 text-center text-muted-foreground">
                                    Пока нет категорий.
                                </td>
                            </tr>
                        )}
                    </tbody>
                </table>
            </div>
        </>
    );
}
