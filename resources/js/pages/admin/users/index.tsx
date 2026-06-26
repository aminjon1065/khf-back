import { Head, Link, router } from '@inertiajs/react';
import { Pencil, Plus, Trash2 } from 'lucide-react';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';

type Row = {
    id: number;
    name: string;
    email: string;
    role: string | null;
    created: string | null;
};

export default function UsersIndex({ items }: { items: Row[] }) {
    const remove = (row: Row) => {
        if (confirm(`Удалить «${row.name}»?`)) {
            router.delete(`/admin/users/${row.id}`, { preserveScroll: true });
        }
    };

    return (
        <>
            <Head title="Пользователи" />

            <div className="flex items-center justify-between gap-4">
                <div>
                    <h1 className="text-2xl font-bold tracking-tight">Пользователи</h1>
                    <p className="mt-1 text-sm text-muted-foreground">Учётные записи и роли.</p>
                </div>
                <Button asChild>
                    <Link href="/admin/users/create">
                        <Plus className="size-4" /> Добавить
                    </Link>
                </Button>
            </div>

            <div className="mt-6 overflow-x-auto rounded-xl border">
                <table className="w-full text-sm">
                    <thead className="bg-muted/50 text-left text-xs uppercase tracking-wide text-muted-foreground">
                        <tr>
                            <th className="p-3 font-medium">Имя</th>
                            <th className="p-3 font-medium">Email</th>
                            <th className="p-3 font-medium">Роль</th>
                            <th className="p-3 font-medium">Создан</th>
                            <th className="p-3" />
                        </tr>
                    </thead>
                    <tbody className="divide-y">
                        {items.map((row) => (
                            <tr key={row.id} className="hover:bg-muted/30">
                                <td className="p-3 font-medium">{row.name}</td>
                                <td className="p-3 text-muted-foreground">{row.email}</td>
                                <td className="p-3">
                                    {row.role ? (
                                        <Badge variant={row.role === 'admin' ? 'default' : 'secondary'}>
                                            {row.role === 'admin' ? 'Администратор' : 'Редактор'}
                                        </Badge>
                                    ) : (
                                        <span className="text-muted-foreground">—</span>
                                    )}
                                </td>
                                <td className="p-3 text-muted-foreground">{row.created ?? '—'}</td>
                                <td className="p-3">
                                    <div className="flex justify-end gap-1">
                                        <Button asChild size="icon" variant="ghost">
                                            <Link href={`/admin/users/${row.id}/edit`} aria-label="Редактировать">
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
                                    Пока нет пользователей.
                                </td>
                            </tr>
                        )}
                    </tbody>
                </table>
            </div>
        </>
    );
}
