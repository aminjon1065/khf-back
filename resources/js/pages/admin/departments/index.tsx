import { Head, Link, router } from '@inertiajs/react';
import { Pencil, Plus, Trash2 } from 'lucide-react';
import { Button } from '@/components/ui/button';

type Row = {
    id: number;
    title: string;
    icon: string | null;
    sort_order: number;
};

export default function DepartmentsIndex({ items }: { items: Row[] }) {
    const remove = (row: Row) => {
        if (confirm(`Удалить «${row.title}»?`)) {
            router.delete(`/admin/departments/${row.id}`, { preserveScroll: true });
        }
    };

    return (
        <>
            <Head title="Отделы" />

            <div className="flex items-center justify-between gap-4">
                <div>
                    <h1 className="text-2xl font-bold tracking-tight">Отделы</h1>
                    <p className="mt-1 text-sm text-muted-foreground">Структурные подразделения.</p>
                </div>
                <Button asChild>
                    <Link href="/admin/departments/create">
                        <Plus className="size-4" /> Добавить
                    </Link>
                </Button>
            </div>

            <div className="mt-6 overflow-x-auto rounded-xl border">
                <table className="w-full text-sm">
                    <thead className="bg-muted/50 text-left text-xs uppercase tracking-wide text-muted-foreground">
                        <tr>
                            <th className="p-3 font-medium">Название</th>
                            <th className="p-3 font-medium">Иконка</th>
                            <th className="p-3" />
                        </tr>
                    </thead>
                    <tbody className="divide-y">
                        {items.map((row) => (
                            <tr key={row.id} className="hover:bg-muted/30">
                                <td className="p-3 font-medium">{row.title}</td>
                                <td className="p-3 text-muted-foreground">{row.icon ?? '—'}</td>
                                <td className="p-3">
                                    <div className="flex justify-end gap-1">
                                        <Button asChild size="icon" variant="ghost">
                                            <Link href={`/admin/departments/${row.id}/edit`} aria-label="Редактировать">
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
