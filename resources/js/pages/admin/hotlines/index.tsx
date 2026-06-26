import { Head, Link, router } from '@inertiajs/react';
import { Pencil, Plus, Trash2 } from 'lucide-react';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';

type Row = {
    id: number;
    number: string;
    label: string;
    is_primary: boolean;
};

export default function HotlinesIndex({ items }: { items: Row[] }) {
    const remove = (row: Row) => {
        if (confirm(`Удалить «${row.label}»?`)) {
            router.delete(`/admin/hotlines/${row.id}`, { preserveScroll: true });
        }
    };

    return (
        <>
            <Head title="Горячие линии" />

            <div className="flex items-center justify-between gap-4">
                <div>
                    <h1 className="text-2xl font-bold tracking-tight">Горячие линии</h1>
                    <p className="mt-1 text-sm text-muted-foreground">Телефоны горячих линий.</p>
                </div>
                <Button asChild>
                    <Link href="/admin/hotlines/create">
                        <Plus className="size-4" /> Добавить
                    </Link>
                </Button>
            </div>

            <div className="mt-6 overflow-x-auto rounded-xl border">
                <table className="w-full text-sm">
                    <thead className="bg-muted/50 text-left text-xs uppercase tracking-wide text-muted-foreground">
                        <tr>
                            <th className="p-3 font-medium">Номер</th>
                            <th className="p-3 font-medium">Название</th>
                            <th className="p-3 font-medium">Основная</th>
                            <th className="p-3" />
                        </tr>
                    </thead>
                    <tbody className="divide-y">
                        {items.map((row) => (
                            <tr key={row.id} className="hover:bg-muted/30">
                                <td className="p-3 font-medium tabular-nums">{row.number}</td>
                                <td className="p-3 text-muted-foreground">{row.label}</td>
                                <td className="p-3">
                                    {row.is_primary ? <Badge>Да</Badge> : <Badge variant="secondary">Нет</Badge>}
                                </td>
                                <td className="p-3">
                                    <div className="flex justify-end gap-1">
                                        <Button asChild size="icon" variant="ghost">
                                            <Link href={`/admin/hotlines/${row.id}/edit`} aria-label="Редактировать">
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
                                    Пока нет горячих линий.
                                </td>
                            </tr>
                        )}
                    </tbody>
                </table>
            </div>
        </>
    );
}
