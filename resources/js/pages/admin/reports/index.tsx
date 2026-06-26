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
    reference: string | null;
    type: string | null;
    region: string | null;
    location: string | null;
    description: string | null;
    phone: string | null;
    status: string;
    created_at: string | null;
};

export default function ReportsIndex({ items, statuses }: { items: Row[]; statuses: Status[] }) {
    const [active, setActive] = useState<Row | null>(null);

    const changeStatus = (row: Row, value: string) => {
        router.patch(`/admin/reports/${row.id}/status`, { status: value }, { preserveScroll: true });
    };

    const remove = (row: Row) => {
        if (confirm('Удалить эту запись?')) {
            router.delete(`/admin/reports/${row.id}`, { preserveScroll: true });
        }
    };

    return (
        <>
            <Head title="Сообщения о происшествиях" />

            <div className="flex items-center justify-between gap-4">
                <div>
                    <h1 className="text-2xl font-bold tracking-tight">Сообщения о происшествиях</h1>
                    <p className="mt-1 text-sm text-muted-foreground">Обращения, поступившие через форму отчёта.</p>
                </div>
            </div>

            <div className="mt-6 overflow-x-auto rounded-xl border">
                <table className="w-full text-sm">
                    <thead className="bg-muted/50 text-left text-xs uppercase tracking-wide text-muted-foreground">
                        <tr>
                            <th className="p-3 font-medium">Номер</th>
                            <th className="p-3 font-medium">Тип</th>
                            <th className="p-3 font-medium">Регион</th>
                            <th className="p-3 font-medium">Место</th>
                            <th className="p-3 font-medium">Телефон</th>
                            <th className="p-3 font-medium">Дата</th>
                            <th className="p-3 font-medium">Статус</th>
                            <th className="p-3" />
                        </tr>
                    </thead>
                    <tbody className="divide-y">
                        {items.map((row) => (
                            <tr key={row.id} className="hover:bg-muted/30">
                                <td className="p-3 font-medium">{row.reference ?? '—'}</td>
                                <td className="p-3 text-muted-foreground">{row.type ?? '—'}</td>
                                <td className="p-3 text-muted-foreground">{row.region ?? '—'}</td>
                                <td className="p-3 text-muted-foreground">{row.location ?? '—'}</td>
                                <td className="p-3 text-muted-foreground tabular-nums">{row.phone ?? '—'}</td>
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
                                <td colSpan={8} className="p-8 text-center text-muted-foreground">
                                    Пока нет сообщений.
                                </td>
                            </tr>
                        )}
                    </tbody>
                </table>
            </div>

            <Dialog open={active !== null} onOpenChange={(open) => !open && setActive(null)}>
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>Сообщение {active?.reference ?? ''}</DialogTitle>
                    </DialogHeader>
                    {active && (
                        <dl className="grid gap-3 text-sm">
                            <Field label="Номер" value={active.reference} />
                            <Field label="Тип" value={active.type} />
                            <Field label="Регион" value={active.region} />
                            <Field label="Место" value={active.location} />
                            <Field label="Телефон" value={active.phone} />
                            <Field label="Дата" value={active.created_at} />
                            <div className="grid gap-1">
                                <dt className="text-xs uppercase tracking-wide text-muted-foreground">Описание</dt>
                                <dd className="whitespace-pre-wrap">{active.description ?? '—'}</dd>
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
