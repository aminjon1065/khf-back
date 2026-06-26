import { Head, Link, router } from '@inertiajs/react';
import { Pencil, Plus, Trash2 } from 'lucide-react';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { cn } from '@/lib/utils';

type Row = {
    id: number;
    title: string;
    slug: string;
    category: string | null;
    status: string;
    published_at: string | null;
    views: number;
    thumb: string | null;
};

type PageLink = { url: string | null; label: string; active: boolean };

export default function NewsIndex({ news }: { news: { data: Row[]; links: PageLink[] } }) {
    const remove = (row: Row) => {
        if (confirm(`Удалить «${row.title}»?`)) {
            router.delete(`/admin/news/${row.id}`, { preserveScroll: true });
        }
    };

    return (
        <>
            <Head title="Новости" />

            <div className="flex items-center justify-between gap-4">
                <div>
                    <h1 className="text-2xl font-bold tracking-tight">Новости</h1>
                    <p className="mt-1 text-sm text-muted-foreground">Лента новостей портала.</p>
                </div>
                <Button asChild>
                    <Link href="/admin/news/create">
                        <Plus className="size-4" /> Добавить
                    </Link>
                </Button>
            </div>

            <div className="mt-6 overflow-x-auto rounded-xl border">
                <table className="w-full text-sm">
                    <thead className="bg-muted/50 text-left text-xs uppercase tracking-wide text-muted-foreground">
                        <tr>
                            <th className="p-3 font-medium">Новость</th>
                            <th className="p-3 font-medium">Категория</th>
                            <th className="p-3 font-medium">Статус</th>
                            <th className="p-3 font-medium">Дата</th>
                            <th className="p-3 text-right font-medium">Просмотры</th>
                            <th className="p-3" />
                        </tr>
                    </thead>
                    <tbody className="divide-y">
                        {news.data.map((row) => (
                            <tr key={row.id} className="hover:bg-muted/30">
                                <td className="p-3">
                                    <div className="flex items-center gap-3">
                                        {row.thumb ? (
                                            <img src={row.thumb} alt="" className="size-10 flex-none rounded-md object-cover" />
                                        ) : (
                                            <div className="size-10 flex-none rounded-md bg-muted" />
                                        )}
                                        <span className="line-clamp-2 max-w-md font-medium">{row.title}</span>
                                    </div>
                                </td>
                                <td className="p-3 text-muted-foreground">{row.category ?? '—'}</td>
                                <td className="p-3">
                                    <Badge variant={row.status === 'published' ? 'default' : 'secondary'}>
                                        {row.status === 'published' ? 'Опубликовано' : 'Черновик'}
                                    </Badge>
                                </td>
                                <td className="p-3 text-muted-foreground">{row.published_at ?? '—'}</td>
                                <td className="p-3 text-right tabular-nums">{row.views.toLocaleString('ru-RU')}</td>
                                <td className="p-3">
                                    <div className="flex justify-end gap-1">
                                        <Button asChild size="icon" variant="ghost">
                                            <Link href={`/admin/news/${row.id}/edit`} aria-label="Редактировать">
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
                        {news.data.length === 0 && (
                            <tr>
                                <td colSpan={6} className="p-8 text-center text-muted-foreground">
                                    Пока нет новостей.
                                </td>
                            </tr>
                        )}
                    </tbody>
                </table>
            </div>

            {news.links.length > 3 && (
                <div className="mt-4 flex flex-wrap gap-1">
                    {news.links.map((link, index) => (
                        <Link
                            key={index}
                            href={link.url ?? '#'}
                            preserveScroll
                            className={cn(
                                'rounded-md border px-3 py-1.5 text-sm transition-colors',
                                link.active ? 'bg-primary text-primary-foreground' : 'hover:bg-muted',
                                !link.url && 'pointer-events-none opacity-40',
                            )}
                            dangerouslySetInnerHTML={{ __html: link.label }}
                        />
                    ))}
                </div>
            )}
        </>
    );
}
