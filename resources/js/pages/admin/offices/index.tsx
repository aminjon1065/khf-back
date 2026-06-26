import { Head, Link, router } from '@inertiajs/react';
import { Pencil, Plus, Trash2 } from 'lucide-react';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';

type Row = {
    id: number;
    region: string;
    phone: string | null;
    is_head: boolean;
};

export default function OfficesIndex({ items }: { items: Row[] }) {
    const remove = (row: Row) => {
        if (confirm(`Удалить «${row.region}»?`)) {
            router.delete(`/admin/offices/${row.id}`, { preserveScroll: true });
        }
    };

    return (
        <>
            <Head title="Офисы" />

            <div className="flex items-center justify-between gap-4">
                <div>
                    <h1 className="text-2xl font-bold tracking-tight">Офисы</h1>
                    <p className="mt-1 text-sm text-muted-foreground">Региональные представительства.</p>
                </div>
                <Button asChild>
                    <Link href="/admin/offices/create">
                        <Plus className="size-4" /> Добавить
                    </Link>
                </Button>
            </div>

            <div className="mt-6 overflow-x-auto rounded-xl border">
                <table className="w-full text-sm">
                    <thead className="bg-muted/50 text-left text-xs uppercase tracking-wide text-muted-foreground">
                        <tr>
                            <th className="p-3 font-medium">Регион</th>
                            <th className="p-3 font-medium">Телефон</th>
                            <th className="p-3 font-medium">Головной</th>
                            <th className="p-3" />
                        </tr>
                    </thead>
                    <tbody className="divide-y">
                        {items.map((row) => (
                            <tr key={row.id} className="hover:bg-muted/30">
                                <td className="p-3 font-medium">{row.region}</td>
                                <td className="p-3 text-muted-foreground">{row.phone ?? '—'}</td>
                                <td className="p-3">
                                    {row.is_head ? <Badge>Да</Badge> : <Badge variant="secondary">Нет</Badge>}
                                </td>
                                <td className="p-3">
                                    <div className="flex justify-end gap-1">
                                        <Button asChild size="icon" variant="ghost">
                                            <Link href={`/admin/offices/${row.id}/edit`} aria-label="Редактировать">
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
                                    Пока нет офисов.
                                </td>
                            </tr>
                        )}
                    </tbody>
                </table>
            </div>
        </>
    );
}
