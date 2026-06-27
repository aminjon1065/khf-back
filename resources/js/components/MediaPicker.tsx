import React, { useState, useEffect } from 'react';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Image as ImageIcon, Upload, X } from 'lucide-react';
import axios from 'axios';

interface MediaItem {
    id: string;
    file_name: string;
    url: string;
    mime_type: string;
    size: number;
}

interface MediaPickerProps {
    value: string | null;
    onChange: (url: string | null) => void;
}

export function MediaPicker({ value, onChange }: MediaPickerProps) {
    const [isOpen, setIsOpen] = useState(false);
    const [media, setMedia] = useState<MediaItem[]>([]);
    const [isLoading, setIsLoading] = useState(false);
    const [isUploading, setIsUploading] = useState(false);

    useEffect(() => {
        if (isOpen && media.length === 0) {
            fetchMedia();
        }
    }, [isOpen]);

    const fetchMedia = async () => {
        setIsLoading(true);
        try {
            const response = await axios.get(route('admin.media.index'), {
                headers: { Accept: 'application/json' },
            });
            setMedia(response.data.data);
        } catch (error) {
            console.error('Failed to fetch media', error);
        } finally {
            setIsLoading(false);
        }
    };

    const handleFileUpload = async (e: React.ChangeEvent<HTMLInputElement>) => {
        const files = e.target.files;
        if (!files || files.length === 0) return;

        setIsUploading(true);
        const formData = new FormData();
        formData.append('file', files[0]);

        try {
            const response = await axios.post(route('admin.media.store'), formData, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                    Accept: 'application/json',
                },
            });
            
            // Add new item to the front of the list
            setMedia((prev) => [response.data, ...prev]);
            
            // Automatically select the newly uploaded file
            onChange(response.data.url);
            setIsOpen(false);
        } catch (error) {
            console.error('Failed to upload file', error);
            alert('Failed to upload file.');
        } finally {
            setIsUploading(false);
        }
    };

    const selectMedia = (url: string) => {
        onChange(url);
        setIsOpen(false);
    };

    return (
        <div className="space-y-4">
            {value ? (
                <div className="relative inline-block border rounded-lg overflow-hidden group">
                    <img src={value} alt="Selected media" className="h-48 w-auto object-contain bg-slate-100" />
                    <button
                        type="button"
                        onClick={() => onChange(null)}
                        className="absolute top-2 right-2 p-1 bg-white/90 text-red-500 rounded-full opacity-0 group-hover:opacity-100 transition-opacity hover:bg-white shadow-sm"
                    >
                        <X className="w-4 h-4" />
                    </button>
                </div>
            ) : (
                <Dialog open={isOpen} onOpenChange={setIsOpen}>
                    <DialogTrigger asChild>
                        <Button type="button" variant="outline" className="w-full h-32 border-dashed flex flex-col items-center justify-center gap-2">
                            <ImageIcon className="w-8 h-8 text-slate-400" />
                            <span className="text-slate-500">Select Media</span>
                        </Button>
                    </DialogTrigger>
                    
                    <DialogContent className="max-w-4xl max-h-[80vh] flex flex-col">
                        <DialogHeader className="flex flex-row justify-between items-center pr-8 border-b pb-4">
                            <DialogTitle>Media Library</DialogTitle>
                            <div className="relative">
                                <input
                                    type="file"
                                    id="media-upload-input"
                                    className="hidden"
                                    onChange={handleFileUpload}
                                    accept="image/*,application/pdf"
                                />
                                <Button 
                                    type="button" 
                                    size="sm" 
                                    disabled={isUploading}
                                    onClick={() => document.getElementById('media-upload-input')?.click()}
                                >
                                    <Upload className="w-4 h-4 mr-2" />
                                    {isUploading ? 'Uploading...' : 'Upload New'}
                                </Button>
                            </div>
                        </DialogHeader>

                        <div className="flex-1 overflow-y-auto min-h-[300px] p-4">
                            {isLoading ? (
                                <div className="flex justify-center items-center h-full text-slate-500">Loading media...</div>
                            ) : media.length === 0 ? (
                                <div className="text-center py-12 text-slate-500">
                                    <ImageIcon className="w-12 h-12 mx-auto mb-4 opacity-50" />
                                    <p>No media files available. Upload one to get started.</p>
                                </div>
                            ) : (
                                <div className="grid grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                                    {media.map((item) => (
                                        <button
                                            key={item.id}
                                            type="button"
                                            onClick={() => selectMedia(item.url)}
                                            className="relative aspect-square rounded-md border border-slate-200 overflow-hidden hover:ring-2 hover:ring-blue-500 transition-all bg-slate-50"
                                        >
                                            {item.mime_type.startsWith('image/') ? (
                                                <img src={item.url} alt={item.file_name} className="w-full h-full object-cover" />
                                            ) : (
                                                <div className="flex items-center justify-center w-full h-full text-xs text-slate-500 p-2 text-center break-all">
                                                    {item.file_name}
                                                </div>
                                            )}
                                        </button>
                                    ))}
                                </div>
                            )}
                        </div>
                    </DialogContent>
                </Dialog>
            )}
        </div>
    );
}
