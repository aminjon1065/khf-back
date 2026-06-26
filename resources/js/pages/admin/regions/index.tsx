import { Head, Link, router } from '@inertiajs/react';
import { Pencil, Plus, Trash2 } from 'lucide-react';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';

type Row = {
    id: number;
    name: string;
    risk: string;
    active_incidents: number;
    stations: number;
};

const riskLabels: Record<string, string> = {
    low: 'Низкий',
    medium: 'Средний',
    high: 'Высокий',
};

const riskVariant = (risk: string) =>
    risk === 'high' ? 'destructive' : risk === 'medium' ? 'default' : 'secondary';

export default function RegionsIndex({ items }: { items: Row[] }) {
    const remove = (row: Row) => {
        if (confirm(`Удалить «${row.name}»?`)) {
            router.delete(`/admin/regions/${row.id}`, { preserveScroll: true });
        }
    };

    return (
        <>
            <Head title="Регионы" />

            <div className="flex items-center justify-between gap-4">
                <div>
                    <h1 className="text-2xl font-bold tracking-tight">Регионы</h1>
                    <p className="mt-1 text-sm text-muted-foreground">Регионы и их показатели риска.</p>
                </div>
                <Button asChild>
                    <Link href="/admin/regions/create">
                        <Plus className="size-4" /> Добавить
                    </Link>
                </Button>
            </div>

            <div className="mt-6 overflow-x-auto rounded-xl border">
                <table className="w-full text-sm">
                    <thead className="bg-muted/50 text-left text-xs uppercase tracking-wide text-muted-foreground">
                        <tr>
                            <th className="p-3 font-medium">Название</th>
                            <th className="p-3 font-medium">Риск</th>
                            <th className="p-3 text-right font-medium">Активные инциденты</th>
                            <th className="p-3 text-right font-medium">Станции</th>
                            <th className="p-3" />
                        </tr>
                    </thead>
                    <tbody className="divide-y">
                        {items.map((row) => (
                            <tr key={row.id} className="hover:bg-muted/30">
                                <td className="p-3 font-medium">{row.name}</td>
                                <td className="p-3">
                                    <Badge variant={riskVariant(row.risk)}>{riskLabels[row.risk] ?? row.risk}</Badge>
                                </td>
                                <td className="p-3 text-right tabular-nums">{row.active_incidents.toLocaleString('ru-RU')}</td>
                                <td className="p-3 text-right tabular-nums">{row.stations.toLocaleString('ru-RU')}</td>
                                <td className="p-3">
                                    <div className="flex justify-end gap-1">
                                        <Button asChild size="icon" variant="ghost">
                                            <Link href={`/admin/regions/${row.id}/edit`} aria-label="Редактировать">
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
                                <td colSpan={5} className="p-8 text-center text-muted-foreground">
                                    Пока нет регионов.
                                </td>
                            </tr>
                        )}
                    </tbody>
                </table>
            </div>
        </>
    );
}
