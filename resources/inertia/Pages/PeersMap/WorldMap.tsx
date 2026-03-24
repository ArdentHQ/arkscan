import { geoNaturalEarth1, geoPath } from "d3-geo";
import { useEffect, useRef, useState } from "react";

import { IPeer } from "@/types/generated";
import { feature } from "topojson-client";

import type { Topology } from "topojson-specification";

interface WorldMapProps {
    peers: IPeer[];
}

const ASPECT_RATIO = 0.5;

function PeerTooltip({ peer, x, y }: { peer: IPeer; x: number; y: number }) {
    const location = [peer.city, peer.country].filter(Boolean).join(", ");

    return (
        <div
            className="pointer-events-none absolute z-10 rounded-lg border border-white/10 bg-[#1c2333] px-3 py-2 text-sm shadow-xl"
            style={{
                left: x,
                top: y,
                transform: "translate(-50%, -100%) translateY(-12px)",
            }}
        >
            <div className="space-y-1">
                <div className="font-medium text-white">{peer.ip}</div>

                {location && <div className="text-gray-400">{location}</div>}

                <div className="text-gray-500 text-xs">Port {peer.port}</div>
            </div>
        </div>
    );
}

export default function WorldMap({ peers }: WorldMapProps) {
    const containerRef = useRef<HTMLDivElement>(null);
    const [dimensions, setDimensions] = useState({ width: 960, height: 480 });
    const [worldData, setWorldData] = useState<GeoJSON.FeatureCollection | null>(null);
    const [hoveredPeer, setHoveredPeer] = useState<{ peer: IPeer; x: number; y: number } | null>(null);

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

    const handlePeerHover = (peer: IPeer, event: React.MouseEvent<SVGCircleElement>) => {
        const svg = event.currentTarget.closest("svg");
        const container = containerRef.current;

        if (!svg || !container) {
            return;
        }

        const svgRect = svg.getBoundingClientRect();
        const containerRect = container.getBoundingClientRect();

        const coords = projection([peer.longitude!, peer.latitude!]);

        if (!coords) {
            return;
        }

        const scaleX = svgRect.width / width;
        const scaleY = svgRect.height / height;

        setHoveredPeer({
            peer,
            x: coords[0] * scaleX + (svgRect.left - containerRect.left),
            y: coords[1] * scaleY + (svgRect.top - containerRect.top),
        });
    };

    return (
        <div ref={containerRef} className="relative w-full">
            <svg viewBox={`0 0 ${width} ${height}`} className="w-full" style={{ background: "#0d1117" }}>
                <defs>
                    <style>
                        {`
                            @keyframes peer-pulse {
                                0% { r: 3; opacity: 0.6; }
                                50% { r: 8; opacity: 0; }
                                100% { r: 3; opacity: 0; }
                            }
                        `}
                    </style>
                </defs>

                {worldData?.features.map((feature, index) => (
                    <path
                        key={index}
                        d={pathGenerator(feature) || ""}
                        fill="#1c2333"
                        stroke="#2d3748"
                        strokeWidth={0.5}
                    />
                ))}

                {peers.map((peer) => {
                    if (peer.latitude === null || peer.longitude === null) {
                        return null;
                    }

                    const coords = projection([peer.longitude, peer.latitude]);

                    if (!coords) {
                        return null;
                    }

                    const isHovered = hoveredPeer?.peer.ip === peer.ip;

                    return (
                        <g key={peer.ip}>
                            <circle
                                cx={coords[0]}
                                cy={coords[1]}
                                r={3}
                                fill="#818cf8"
                                opacity={0.3}
                                style={{
                                    animation: "peer-pulse 3s ease-in-out infinite",
                                    animationDelay: `${Math.random() * 3}s`,
                                }}
                            />

                            <circle cx={coords[0]} cy={coords[1]} r={3} fill="#818cf8" opacity={isHovered ? 1 : 0.8} />

                            <circle
                                cx={coords[0]}
                                cy={coords[1]}
                                r={8}
                                fill="transparent"
                                className="cursor-pointer"
                                onMouseEnter={(e) => handlePeerHover(peer, e)}
                                onMouseLeave={() => setHoveredPeer(null)}
                            />
                        </g>
                    );
                })}
            </svg>

            {hoveredPeer && <PeerTooltip peer={hoveredPeer.peer} x={hoveredPeer.x} y={hoveredPeer.y} />}
        </div>
    );
}
