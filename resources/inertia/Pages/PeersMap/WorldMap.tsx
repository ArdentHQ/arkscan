import { geoNaturalEarth1, geoPath } from "d3-geo";
import { useEffect, useMemo, useRef, useState } from "react";

import { IPeer } from "@/types/generated";
import { feature } from "topojson-client";

import type { Topology } from "topojson-specification";

interface PeerGroup {
    latitude: number;
    longitude: number;
    location: string;
    count: number;
}

interface WorldMapProps {
    peers: IPeer[];
}

const ASPECT_RATIO = 0.5;

function groupPeersByLocation(peers: IPeer[]): PeerGroup[] {
    const groups = new Map<string, { latSum: number; lonSum: number; count: number; location: string }>();

    for (const peer of peers) {
        if (peer.latitude === null || peer.longitude === null) {
            continue;
        }

        const key = [peer.city, peer.country].filter(Boolean).join(", ") || `${peer.latitude},${peer.longitude}`;
        const location = [peer.city, peer.country].filter(Boolean).join(", ");

        const existing = groups.get(key);

        if (existing) {
            existing.latSum += peer.latitude;
            existing.lonSum += peer.longitude;
            existing.count++;
        } else {
            groups.set(key, {
                latSum: peer.latitude,
                lonSum: peer.longitude,
                count: 1,
                location,
            });
        }
    }

    return Array.from(groups.values()).map((group) => ({
        latitude: group.latSum / group.count,
        longitude: group.lonSum / group.count,
        location: group.location,
        count: group.count,
    }));
}

function PeerTooltip({ group, x, y, flipped }: { group: PeerGroup; x: number; y: number; flipped: boolean }) {
    return (
        <div
            className="pointer-events-none absolute z-10 rounded-lg border border-white/10 bg-[#1c2333] px-3 py-2 text-sm shadow-xl"
            style={{
                left: x,
                top: y,
                transform: flipped ? "translate(-50%, 12px)" : "translate(-50%, -100%) translateY(-12px)",
            }}
        >
            <div className="space-y-1">
                <div className="font-medium text-white">{group.count === 1 ? "1 Peer" : `${group.count} Peers`}</div>

                {group.location && <div className="text-gray-400">{group.location}</div>}
            </div>
        </div>
    );
}

export default function WorldMap({ peers }: WorldMapProps) {
    const containerRef = useRef<HTMLDivElement>(null);
    const [dimensions, setDimensions] = useState({ width: 960, height: 480 });
    const [worldData, setWorldData] = useState<GeoJSON.FeatureCollection | null>(null);
    const [hoveredGroup, setHoveredGroup] = useState<{
        group: PeerGroup;
        x: number;
        y: number;
        flipped: boolean;
    } | null>(null);
    const [zoom, setZoom] = useState(1);
    const [pan, setPan] = useState({ x: 0, y: 0 });
    const [isPanning, setIsPanning] = useState(false);
    const panStart = useRef({ x: 0, y: 0, panX: 0, panY: 0 });

    const peerGroups = useMemo(() => groupPeersByLocation(peers), [peers]);

    const pulseDelays = useMemo(() => peerGroups.map(() => Math.random() * 10), [peerGroups]);

    const pulseDurations = useMemo(() => peerGroups.map(() => 4 + Math.random() * 4), [peerGroups]);

    useEffect(() => {
        import("world-atlas/countries-110m.json").then((topology) => {
            const topo = topology.default as unknown as Topology;
            const countries = feature(topo, topo.objects.countries) as unknown as GeoJSON.FeatureCollection;
            setWorldData(countries);
        });
    }, []);

    useEffect(() => {
        const updateDimensions = () => {
            if (containerRef.current) {
                const width = containerRef.current.clientWidth;
                setDimensions({ width, height: width * ASPECT_RATIO });
            }
        };

        updateDimensions();

        window.addEventListener("resize", updateDimensions);

        return () => window.removeEventListener("resize", updateDimensions);
    }, []);

    const { width, height } = dimensions;

    const projection = geoNaturalEarth1()
        .scale(width / 5.5)
        .translate([width / 2, height / 2]);

    const pathGenerator = geoPath().projection(projection);

    const handleGroupHover = (group: PeerGroup, event: React.MouseEvent<SVGCircleElement>) => {
        const svg = event.currentTarget.closest("svg");
        const container = containerRef.current;

        if (!svg || !container) {
            return;
        }

        const svgRect = svg.getBoundingClientRect();
        const containerRect = container.getBoundingClientRect();

        const coords = projection([group.longitude, group.latitude]);

        if (!coords) {
            return;
        }

        const scaleX = svgRect.width / width;
        const scaleY = svgRect.height / height;

        const tooltipY = coords[1] * scaleY + (svgRect.top - containerRect.top);
        const flipped = tooltipY < 60;

        setHoveredGroup({
            group,
            x: coords[0] * scaleX + (svgRect.left - containerRect.left),
            y: tooltipY,
            flipped,
        });
    };

    const zoomToPoint = (newZoom: number, clientX: number, clientY: number) => {
        const clamped = Math.min(Math.max(newZoom, 1), 8);

        if (clamped === 1) {
            setPan({ x: 0, y: 0 });
            setZoom(1);
            return;
        }

        const svg = containerRef.current?.querySelector("svg");

        if (!svg) {
            setZoom(clamped);
            return;
        }

        const rect = svg.getBoundingClientRect();
        const mouseX = clientX - rect.left;
        const mouseY = clientY - rect.top;

        const svgX = (mouseX / rect.width) * width;
        const svgY = (mouseY / rect.height) * height;

        const pointX = (svgX - width / 2 - pan.x) / zoom;
        const pointY = (svgY - height / 2 - pan.y) / zoom;

        const newPanX = svgX - width / 2 - pointX * clamped;
        const newPanY = svgY - height / 2 - pointY * clamped;

        setPan({ x: newPanX, y: newPanY });
        setZoom(clamped);
    };

    const handleWheel = (event: React.WheelEvent) => {
        event.preventDefault();

        const delta = event.deltaY > 0 ? -0.3 : 0.3;

        zoomToPoint(zoom + delta, event.clientX, event.clientY);
    };

    const handleZoomButton = (direction: number) => {
        const container = containerRef.current;

        if (!container) {
            return;
        }

        const rect = container.getBoundingClientRect();

        zoomToPoint(zoom + direction * 0.5, rect.left + rect.width / 2, rect.top + rect.height / 2);
    };

    const handleMouseDown = (event: React.MouseEvent) => {
        if (zoom <= 1) {
            return;
        }

        setIsPanning(true);
        panStart.current = { x: event.clientX, y: event.clientY, panX: pan.x, panY: pan.y };
    };

    const handleMouseMove = (event: React.MouseEvent) => {
        if (!isPanning) {
            return;
        }

        const dx = event.clientX - panStart.current.x;
        const dy = event.clientY - panStart.current.y;

        setPan({
            x: panStart.current.panX + dx,
            y: panStart.current.panY + dy,
        });
    };

    const handleMouseUp = () => {
        setIsPanning(false);
    };

    const dotRadius = Math.max(2, 3 / Math.sqrt(zoom));

    return (
        <div ref={containerRef} className="relative w-full">
            <div className="absolute right-2 top-2 z-20 flex flex-col gap-1">
                <button
                    type="button"
                    className="rounded-md bg-white/10 px-2 py-1 text-xs text-white hover:bg-white/20 disabled:opacity-30"
                    disabled={zoom >= 8}
                    onClick={() => handleZoomButton(1)}
                >
                    +
                </button>

                <button
                    type="button"
                    className="rounded-md bg-white/10 px-2 py-1 text-xs text-white hover:bg-white/20 disabled:opacity-30"
                    disabled={zoom <= 1}
                    onClick={() => handleZoomButton(-1)}
                >
                    &minus;
                </button>

                {zoom > 1 && (
                    <button
                        type="button"
                        className="mt-1 rounded-md bg-white/10 px-2 py-1 text-xs text-white hover:bg-white/20"
                        onClick={() => {
                            setZoom(1);
                            setPan({ x: 0, y: 0 });
                        }}
                    >
                        Reset
                    </button>
                )}
            </div>

            <svg
                viewBox={`0 0 ${width} ${height}`}
                className="w-full"
                style={{ background: "#0d1117", cursor: zoom > 1 ? (isPanning ? "grabbing" : "grab") : "default" }}
                onWheel={handleWheel}
                onMouseDown={handleMouseDown}
                onMouseMove={handleMouseMove}
                onMouseUp={handleMouseUp}
                onMouseLeave={handleMouseUp}
            >
                <g
                    transform={`translate(${width / 2 + pan.x}, ${height / 2 + pan.y}) scale(${zoom}) translate(${-width / 2}, ${-height / 2})`}
                >
                    {worldData?.features.map((feature, index) => (
                        <path
                            key={index}
                            d={pathGenerator(feature) || ""}
                            fill="#1c2333"
                            stroke="#2d3748"
                            strokeWidth={0.5 / zoom}
                        />
                    ))}

                    {peerGroups.map((group, index) => {
                        const coords = projection([group.longitude, group.latitude]);

                        if (!coords) {
                            return null;
                        }

                        const isHovered = hoveredGroup?.group === group;
                        const duration = pulseDurations[index];
                        const delay = pulseDelays[index];

                        return (
                            <g key={index}>
                                <circle cx={coords[0]} cy={coords[1]} r={dotRadius} fill="#818cf8" opacity={0}>
                                    <animate
                                        attributeName="r"
                                        values={`${dotRadius};${dotRadius + 5};${dotRadius}`}
                                        dur={`${duration}s`}
                                        begin={`${delay}s`}
                                        repeatCount="indefinite"
                                    />
                                    <animate
                                        attributeName="opacity"
                                        values="0.5;0;0"
                                        dur={`${duration}s`}
                                        begin={`${delay}s`}
                                        repeatCount="indefinite"
                                    />
                                </circle>

                                <circle
                                    cx={coords[0]}
                                    cy={coords[1]}
                                    r={dotRadius}
                                    fill="#818cf8"
                                    opacity={isHovered ? 1 : 0.8}
                                />

                                <circle
                                    cx={coords[0]}
                                    cy={coords[1]}
                                    r={Math.max(8, 8 / zoom)}
                                    fill="transparent"
                                    className="cursor-pointer"
                                    onMouseEnter={(e) => handleGroupHover(group, e)}
                                    onMouseLeave={() => setHoveredGroup(null)}
                                />
                            </g>
                        );
                    })}
                </g>
            </svg>

            {hoveredGroup && (
                <PeerTooltip
                    group={hoveredGroup.group}
                    x={hoveredGroup.x}
                    y={hoveredGroup.y}
                    flipped={hoveredGroup.flipped}
                />
            )}
        </div>
    );
}
