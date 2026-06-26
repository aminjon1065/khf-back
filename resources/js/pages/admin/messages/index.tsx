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
    name: string | null;
    email: string | null;
    subject: string | null;
    message: string | null;
    status: string;
    created_at: string | null;
};

export default function MessagesIndex({ items, statuses }: { items: Row[]; statuses: Status[] }) {
    const [active, setActive] = useState<Row | null>(null);

    const changeStatus = (row: Row, value: string) => {
        router.patch(`/admin/messages/${row.id}/status`, { status: value }, { preserveScroll: true });
    };

    const remove = (row: Row) => {
        if (confirm('Удалить это сообщение?')) {
            router.delete(`/admin/messages/${row.id}`, { preserveScroll: true });
        }
    };

    return (
        <>
            <Head title="Обращения" />

            <div className="flex items-center justify-between gap-4">
                <div>
                    <h1 className="text-2xl font-bold tracking-tight">Обращения</h1>
                    <p className="mt-1 text-sm text-muted-foreground">Сообщения, отправленные через форму обратной связи.</p>
                </div>
            </div>

            <div className="mt-6 overflow-x-auto rounded-xl border">
                <table className="w-full text-sm">
                    <thead className="bg-muted/50 text-left text-xs uppercase tracking-wide text-muted-foreground">
                        <tr>
                            <th className="p-3 font-medium">Имя</th>
                            <th className="p-3 font-medium">Email</th>
                            <th className="p-3 font-medium">Тема</th>
                            <th className="p-3 font-medium">Сообщение</th>
                            <th className="p-3 font-medium">Дата</th>
                            <th className="p-3 font-medium">Статус</th>
                            <th className="p-3" />
                        </tr>
                    </thead>
                    <tbody className="divide-y">
                        {items.map((row) => (
                            <tr key={row.id} className="hover:bg-muted/30">
                                <td className="p-3 font-medium">{row.name ?? '—'}</td>
                                <td className="p-3 text-muted-foreground">{row.email ?? '—'}</td>
                                <td className="p-3 text-muted-foreground">{row.subject ?? '—'}</td>
                                <td className="p-3 text-muted-foreground">
                                    <span className="line-clamp-1 max-w-xs">{row.message ?? '—'}</span>
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
                                    Пока нет обращений.
                                </td>
                            </tr>
                        )}
                    </tbody>
                </table>
            </div>

            <Dialog open={active !== null} onOpenChange={(open) => !open && setActive(null)}>
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>{active?.subject ?? 'Обращение'}</DialogTitle>
                    </DialogHeader>
                    {active && (
                        <dl className="grid gap-3 text-sm">
                            <Field label="Имя" value={active.name} />
                            <Field label="Email" value={active.email} />
                            <Field label="Тема" value={active.subject} />
                            <Field label="Дата" value={active.created_at} />
                            <div className="grid gap-1">
                                <dt className="text-xs uppercase tracking-wide text-muted-foreground">Сообщение</dt>
                                <dd className="whitespace-pre-wrap">{active.message ?? '—'}</dd>
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
