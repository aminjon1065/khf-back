import { Head } from '@inertiajs/react';
import { Bell, FileText, Inbox, Mail, Newspaper } from 'lucide-react';
import { Card } from '@/components/ui/card';

type Stats = {
    news: number;
    documents: number;
    reports: number;
    messages: number;
    subscriptions: number;
};

const CARDS = [
    { key: 'news', label: 'Новости', icon: Newspaper },
    { key: 'documents', label: 'Документы', icon: FileText },
    { key: 'reports', label: 'Новые заявки', icon: Inbox },
    { key: 'messages', label: 'Новые сообщения', icon: Mail },
    { key: 'subscriptions', label: 'Подписки', icon: Bell },
] as const;

export default function Dashboard({ stats }: { stats: Stats }) {
    return (
        <>
            <Head title="Дашборд" />
            <h1 className="text-2xl font-bold tracking-tight">Дашборд</h1>
            <p className="mt-1 text-sm text-muted-foreground">Обзор контента и обращений портала КҲФ.</p>

            <div className="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5">
                {CARDS.map(({ key, label, icon: Icon }) => (
                    <Card key={key} className="p-5">
                        <div className="flex items-center justify-between">
                            <span className="text-sm text-muted-foreground">{label}</span>
                            <Icon className="size-5 text-muted-foreground" />
                        </div>
                        <div className="mt-3 text-3xl font-bold tabular-nums">{stats[key]}</div>
                    </Card>
                ))}
            </div>
        </>
    );
}
