import { Head, Link, router } from '@inertiajs/react';
import { Pencil, Plus, Trash2 } from 'lucide-react';
import { Button } from '@/components/ui/button';

type Row = {
    id: number;
    category: string;
    title: string;
    date: string | null;
    source: string | null;
    sort_order: number;
    thumb: string | null;
};

export default function SlidesIndex({ items }: { items: Row[] }) {
    const remove = (row: Row) => {
        if (confirm(`Удалить «${row.title}»?`)) {
            router.delete(`/admin/slides/${row.id}`, { preserveScroll: true });
        }
    };

    return (
        <>
            <Head title="Слайды" />

            <div className="flex items-center justify-between gap-4">
                <div>
                    <h1 className="text-2xl font-bold tracking-tight">Слайды</h1>
                    <p className="mt-1 text-sm text-muted-foreground">Слайдер на главной странице.</p>
                </div>
                <Button asChild>
                    <Link href="/admin/slides/create">
                        <Plus className="size-4" /> Добавить
                    </Link>
                </Button>
            </div>

            <div className="mt-6 overflow-x-auto rounded-xl border">
                <table className="w-full text-sm">
                    <thead className="bg-muted/50 text-left text-xs uppercase tracking-wide text-muted-foreground">
                        <tr>
                            <th className="p-3 font-medium">Слайд</th>
                            <th className="p-3 font-medium">Источник</th>
                            <th className="p-3" />
                        </tr>
                    </thead>
                    <tbody className="divide-y">
                        {items.map((row) => (
                            <tr key={row.id} className="hover:bg-muted/30">
                                <td className="p-3">
                                    <div className="flex items-center gap-3">
                                        {row.thumb ? (
                                            <img src={row.thumb} alt="" className="size-10 flex-none rounded-md object-cover" />
                                        ) : (
                                            <div className="size-10 flex-none rounded-md bg-muted" />
                                        )}
                                        <span className="line-clamp-2 max-w-md font-medium">{row.title}</span>
                                    </div>
                                </td>
                                <td className="p-3 text-muted-foreground">{row.source ?? '—'}</td>
                                <td className="p-3">
                                    <div className="flex justify-end gap-1">
                                        <Button asChild size="icon" variant="ghost">
                                            <Link href={`/admin/slides/${row.id}/edit`} aria-label="Редактировать">
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
                                <td colSpan={3} className="p-8 text-center text-muted-foreground">
                                    Пока нет слайдов.
                                </td>
                            </tr>
                        )}
                    </tbody>
                </table>
            </div>
        </>
    );
}
