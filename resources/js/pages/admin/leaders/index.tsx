import { Head, Link, router } from '@inertiajs/react';
import { Pencil, Plus, Trash2 } from 'lucide-react';
import { Button } from '@/components/ui/button';

type Row = {
    id: number;
    name: string;
    role: string;
    sort_order: number;
    thumb: string | null;
};

export default function LeadersIndex({ items }: { items: Row[] }) {
    const remove = (row: Row) => {
        if (confirm(`Удалить «${row.name}»?`)) {
            router.delete(`/admin/leaders/${row.id}`, { preserveScroll: true });
        }
    };

    return (
        <>
            <Head title="Руководство" />

            <div className="flex items-center justify-between gap-4">
                <div>
                    <h1 className="text-2xl font-bold tracking-tight">Руководство</h1>
                    <p className="mt-1 text-sm text-muted-foreground">Руководители организации.</p>
                </div>
                <Button asChild>
                    <Link href="/admin/leaders/create">
                        <Plus className="size-4" /> Добавить
                    </Link>
                </Button>
            </div>

            <div className="mt-6 overflow-x-auto rounded-xl border">
                <table className="w-full text-sm">
                    <thead className="bg-muted/50 text-left text-xs uppercase tracking-wide text-muted-foreground">
                        <tr>
                            <th className="p-3 font-medium">Фото</th>
                            <th className="p-3 font-medium">Имя</th>
                            <th className="p-3 font-medium">Должность</th>
                            <th className="p-3" />
                        </tr>
                    </thead>
                    <tbody className="divide-y">
                        {items.map((row) => (
                            <tr key={row.id} className="hover:bg-muted/30">
                                <td className="p-3">
                                    {row.thumb ? (
                                        <img src={row.thumb} alt="" className="size-10 flex-none rounded-md object-cover" />
                                    ) : (
                                        <div className="size-10 flex-none rounded-md bg-muted" />
                                    )}
                                </td>
                                <td className="p-3 font-medium">{row.name}</td>
                                <td className="p-3 text-muted-foreground">{row.role}</td>
                                <td className="p-3">
                                    <div className="flex justify-end gap-1">
                                        <Button asChild size="icon" variant="ghost">
                                            <Link href={`/admin/leaders/${row.id}/edit`} aria-label="Редактировать">
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
                                    Пока нет записей.
                                </td>
                            </tr>
                        )}
                    </tbody>
                </table>
            </div>
        </>
    );
}
