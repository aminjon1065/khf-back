import { Head, useForm } from '@inertiajs/react';
import { Save } from 'lucide-react';
import { TranslatableField } from '@/components/admin/translatable-field';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

type LocaleMap = Record<string, string>;

type President = {
    name: string;
    role: LocaleMap;
    quote: LocaleMap;
    href: string;
};

type SiteStats = { today: string; month: string; rescued: string; reaction: string };
type ForumStats = { members: string; topics: string; posts: string; online: string };
type MapStats = { regions: string; stations: string; activeIncidents: string; monitoring: string };

type Props = {
    locales: string[];
    president: President | null;
    siteStats: SiteStats | null;
    forumStats: ForumStats | null;
    mapStats: MapStats | null;
};

export default function SettingsIndex({ locales, president, siteStats, forumStats, mapStats }: Props) {
    const blank = Object.fromEntries(locales.map((l) => [l, ''])) as LocaleMap;

    const form = useForm({
        president: {
            name: president?.name ?? '',
            role: president?.role ?? { ...blank },
            quote: president?.quote ?? { ...blank },
            href: president?.href ?? '',
        },
        site_stats: {
            today: siteStats?.today ?? '',
            month: siteStats?.month ?? '',
            rescued: siteStats?.rescued ?? '',
            reaction: siteStats?.reaction ?? '',
        },
        forum_stats: {
            members: forumStats?.members ?? '',
            topics: forumStats?.topics ?? '',
            posts: forumStats?.posts ?? '',
            online: forumStats?.online ?? '',
        },
        map_stats: {
            regions: mapStats?.regions ?? '',
            stations: mapStats?.stations ?? '',
            activeIncidents: mapStats?.activeIncidents ?? '',
            monitoring: mapStats?.monitoring ?? '',
        },
    });

    const errors = form.errors as Record<string, string>;

    const submit = (event: React.FormEvent) => {
        event.preventDefault();
        form.put('/admin/settings');
    };

    return (
        <>
            <Head title="Настройки" />

            <form onSubmit={submit}>
                <div className="flex items-center justify-between gap-4">
                    <div>
                        <h1 className="text-2xl font-bold tracking-tight">Настройки</h1>
                        <p className="mt-1 text-sm text-muted-foreground">Общие данные и статистика портала.</p>
                    </div>
                    <Button type="submit" disabled={form.processing}>
                        <Save className="size-4" /> Сохранить
                    </Button>
                </div>

                <div className="mt-6 grid gap-6">
                    {/* Президент */}
                    <Card>
                        <CardHeader>
                            <CardTitle>Президент</CardTitle>
                        </CardHeader>
                        <CardContent className="grid gap-5">
                            <div className="grid gap-2">
                                <Label>Имя</Label>
                                <Input
                                    value={form.data.president.name}
                                    onChange={(event) =>
                                        form.setData('president', { ...form.data.president, name: event.target.value })
                                    }
                                    placeholder="Имя президента"
                                />
                            </div>

                            <TranslatableField
                                name="president.role"
                                label="Должность"
                                locales={locales}
                                value={form.data.president.role}
                                onChange={(value) => form.setData('president', { ...form.data.president, role: value })}
                                errors={errors}
                            />

                            <TranslatableField
                                name="president.quote"
                                label="Цитата"
                                as="textarea"
                                locales={locales}
                                value={form.data.president.quote}
                                onChange={(value) => form.setData('president', { ...form.data.president, quote: value })}
                                errors={errors}
                            />

                            <div className="grid gap-2">
                                <Label>Ссылка</Label>
                                <Input
                                    value={form.data.president.href}
                                    onChange={(event) =>
                                        form.setData('president', { ...form.data.president, href: event.target.value })
                                    }
                                    placeholder="/about/president"
                                />
                            </div>
                        </CardContent>
                    </Card>

                    {/* Статистика сайта */}
                    <Card>
                        <CardHeader>
                            <CardTitle>Статистика сайта</CardTitle>
                        </CardHeader>
                        <CardContent className="grid gap-5 sm:grid-cols-2">
                            <div className="grid gap-2">
                                <Label>Сегодня</Label>
                                <Input
                                    value={form.data.site_stats.today}
                                    onChange={(event) =>
                                        form.setData('site_stats', { ...form.data.site_stats, today: event.target.value })
                                    }
                                />
                            </div>
                            <div className="grid gap-2">
                                <Label>Месяц</Label>
                                <Input
                                    value={form.data.site_stats.month}
                                    onChange={(event) =>
                                        form.setData('site_stats', { ...form.data.site_stats, month: event.target.value })
                                    }
                                />
                            </div>
                            <div className="grid gap-2">
                                <Label>Спасено</Label>
                                <Input
                                    value={form.data.site_stats.rescued}
                                    onChange={(event) =>
                                        form.setData('site_stats', { ...form.data.site_stats, rescued: event.target.value })
                                    }
                                />
                            </div>
                            <div className="grid gap-2">
                                <Label>Реакция</Label>
                                <Input
                                    value={form.data.site_stats.reaction}
                                    onChange={(event) =>
                                        form.setData('site_stats', { ...form.data.site_stats, reaction: event.target.value })
                                    }
                                />
                            </div>
                        </CardContent>
                    </Card>

                    {/* Форум */}
                    <Card>
                        <CardHeader>
                            <CardTitle>Форум</CardTitle>
                        </CardHeader>
                        <CardContent className="grid gap-5 sm:grid-cols-2">
                            <div className="grid gap-2">
                                <Label>Участники</Label>
                                <Input
                                    value={form.data.forum_stats.members}
                                    onChange={(event) =>
                                        form.setData('forum_stats', { ...form.data.forum_stats, members: event.target.value })
                                    }
                                />
                            </div>
                            <div className="grid gap-2">
                                <Label>Темы</Label>
                                <Input
                                    value={form.data.forum_stats.topics}
                                    onChange={(event) =>
                                        form.setData('forum_stats', { ...form.data.forum_stats, topics: event.target.value })
                                    }
                                />
                            </div>
                            <div className="grid gap-2">
                                <Label>Сообщения</Label>
                                <Input
                                    value={form.data.forum_stats.posts}
                                    onChange={(event) =>
                                        form.setData('forum_stats', { ...form.data.forum_stats, posts: event.target.value })
                                    }
                                />
                            </div>
                            <div className="grid gap-2">
                                <Label>Онлайн</Label>
                                <Input
                                    value={form.data.forum_stats.online}
                                    onChange={(event) =>
                                        form.setData('forum_stats', { ...form.data.forum_stats, online: event.target.value })
                                    }
                                />
                            </div>
                        </CardContent>
                    </Card>

                    {/* Карта */}
                    <Card>
                        <CardHeader>
                            <CardTitle>Карта</CardTitle>
                        </CardHeader>
                        <CardContent className="grid gap-5 sm:grid-cols-2">
                            <div className="grid gap-2">
                                <Label>Регионы</Label>
                                <Input
                                    value={form.data.map_stats.regions}
                                    onChange={(event) =>
                                        form.setData('map_stats', { ...form.data.map_stats, regions: event.target.value })
                                    }
                                />
                            </div>
                            <div className="grid gap-2">
                                <Label>Станции</Label>
                                <Input
                                    value={form.data.map_stats.stations}
                                    onChange={(event) =>
                                        form.setData('map_stats', { ...form.data.map_stats, stations: event.target.value })
                                    }
                                />
                            </div>
                            <div className="grid gap-2">
                                <Label>Активные инциденты</Label>
                                <Input
                                    value={form.data.map_stats.activeIncidents}
                                    onChange={(event) =>
                                        form.setData('map_stats', {
                                            ...form.data.map_stats,
                                            activeIncidents: event.target.value,
                                        })
                                    }
                                />
                            </div>
                            <div className="grid gap-2">
                                <Label>Мониторинг</Label>
                                <Input
                                    value={form.data.map_stats.monitoring}
                                    onChange={(event) =>
                                        form.setData('map_stats', { ...form.data.map_stats, monitoring: event.target.value })
                                    }
                                />
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </form>
        </>
    );
}
