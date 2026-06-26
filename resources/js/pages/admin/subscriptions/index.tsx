import { Head, router } from '@inertiajs/react';
import { Eye, Trash2 } from 'lucide-react';
import { useState } from 'react';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';

type Status = { value: string; label: string };

type Row = {
    id: number;
    channel: string | null;
    region: string | null;
    contact: string | null;
    categories: string[] | null;
    status: string;
    created_at: string | null;
};

export default function SubscriptionsIndex({ items, statuses }: { items: Row[]; statuses: Status[] }) {
    const [active, setActive] = useState<Row | null>(null);

    const changeStatus = (row: Row, value: string) => {
        router.patch(`/admin/subscriptions/${row.id}/status`, { status: value }, { preserveScroll: true });
    };

    const remove = (row: Row) => {
        if (confirm('Удалить эту подписку?')) {
            router.delete(`/admin/subscriptions/${row.id}`, { preserveScroll: true });
        }
    };

    return (
        <>
            <Head title="Подписки" />

            <div className="flex items-center justify-between gap-4">
                <div>
                    <h1 className="text-2xl font-bold tracking-tight">Подписки</h1>
                    <p className="mt-1 text-sm text-muted-foreground">Заявки на подписку на уведомления.</p>
                </div>
            </div>

            <div className="mt-6 overflow-x-auto rounded-xl border">
                <table className="w-full text-sm">
                    <thead className="bg-muted/50 text-left text-xs uppercase tracking-wide text-muted-foreground">
                        <tr>
                            <th className="p-3 font-medium">Канал</th>
                            <th className="p-3 font-medium">Регион</th>
                            <th className="p-3 font-medium">Контакт</th>
                            <th className="p-3 font-medium">Категории</th>
                            <th className="p-3 font-medium">Дата</th>
                            <th className="p-3 font-medium">Статус</th>
                            <th className="p-3" />
                        </tr>
                    </thead>
                    <tbody className="divide-y">
                        {items.map((row) => (
                            <tr key={row.id} className="hover:bg-muted/30">
                                <td className="p-3 font-medium">{row.channel ?? '—'}</td>
                                <td className="p-3 text-muted-foreground">{row.region ?? '—'}</td>
                                <td className="p-3 text-muted-foreground">{row.contact ?? '—'}</td>
                                <td className="p-3 text-muted-foreground">
                                    {row.categories && row.categories.length > 0 ? row.categories.join(', ') : '—'}
                                </td>
                                <td className="p-3 text-muted-foreground">{row.created_at ?? '—'}</td>
                                <td className="p-3">
                                    <Select value={row.status} onValueChange={(value) => changeStatus(row, value)}>
                                        <SelectTrigger className="w-40">
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
                                </td>
                                <td className="p-3">
                                    <div className="flex justify-end gap-1">
                                        <Button size="icon" variant="ghost" onClick={() => setActive(row)} aria-label="Подробнее">
                                            <Eye className="size-4" />
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
                                <td colSpan={7} className="p-8 text-center text-muted-foreground">
                                    Пока нет подписок.
                                </td>
                            </tr>
                        )}
                    </tbody>
                </table>
            </div>

            <Dialog open={active !== null} onOpenChange={(open) => !open && setActive(null)}>
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>Подписка</DialogTitle>
                    </DialogHeader>
                    {active && (
                        <dl className="grid gap-3 text-sm">
                            <Field label="Канал" value={active.channel} />
                            <Field label="Регион" value={active.region} />
                            <Field label="Контакт" value={active.contact} />
                            <Field label="Дата" value={active.created_at} />
                            <div className="grid gap-1">
                                <dt className="text-xs uppercase tracking-wide text-muted-foreground">Категории</dt>
                                <dd>
                                    {active.categories && active.categories.length > 0
                                        ? active.categories.join(', ')
                                        : '—'}
                                </dd>
                            </div>
                        </dl>
                    )}
                </DialogContent>
            </Dialog>
        </>
    );
}

function Field({ label, value }: { label: string; value: string | null }) {
    return (
        <div className="grid gap-1">
            <dt className="text-xs uppercase tracking-wide text-muted-foreground">{label}</dt>
            <dd>{value ?? '—'}</dd>
        </div>
    );
}
