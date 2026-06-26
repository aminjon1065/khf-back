import { Head, Link, router } from '@inertiajs/react';
import { Pencil, Plus, Trash2 } from 'lucide-react';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';

type Row = {
    id: number;
    title: string;
    author: string | null;
    pinned: boolean;
    replies: number;
};

export default function ForumTopicsIndex({ items }: { items: Row[] }) {
    const remove = (row: Row) => {
        if (confirm(`Удалить «${row.title}»?`)) {
            router.delete(`/admin/forum-topics/${row.id}`, { preserveScroll: true });
        }
    };

    return (
        <>
            <Head title="Темы форума" />

            <div className="flex items-center justify-between gap-4">
                <div>
                    <h1 className="text-2xl font-bold tracking-tight">Темы форума</h1>
                    <p className="mt-1 text-sm text-muted-foreground">Темы обсуждений на форуме.</p>
                </div>
                <Button asChild>
                    <Link href="/admin/forum-topics/create">
                        <Plus className="size-4" /> Добавить
                    </Link>
                </Button>
            </div>

            <div className="mt-6 overflow-x-auto rounded-xl border">
                <table className="w-full text-sm">
                    <thead className="bg-muted/50 text-left text-xs uppercase tracking-wide text-muted-foreground">
                        <tr>
                            <th className="p-3 font-medium">Тема</th>
                            <th className="p-3 font-medium">Автор</th>
                            <th className="p-3 font-medium">Закреплена</th>
                            <th className="p-3 text-right font-medium">Ответы</th>
                            <th className="p-3" />
                        </tr>
                    </thead>
                    <tbody className="divide-y">
                        {items.map((row) => (
                            <tr key={row.id} className="hover:bg-muted/30">
                                <td className="p-3">
                                    <span className="line-clamp-2 max-w-md font-medium">{row.title}</span>
                                </td>
                                <td className="p-3 text-muted-foreground">{row.author ?? '—'}</td>
                                <td className="p-3">
                                    <Badge variant={row.pinned ? 'default' : 'secondary'}>
                                        {row.pinned ? 'Да' : 'Нет'}
                                    </Badge>
                                </td>
                                <td className="p-3 text-right tabular-nums">{row.replies.toLocaleString('ru-RU')}</td>
                                <td className="p-3">
                                    <div className="flex justify-end gap-1">
                                        <Button asChild size="icon" variant="ghost">
                                            <Link href={`/admin/forum-topics/${row.id}/edit`} aria-label="Редактировать">
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
                                    Пока нет тем.
                                </td>
                            </tr>
                        )}
                    </tbody>
                </table>
            </div>
        </>
    );
}
