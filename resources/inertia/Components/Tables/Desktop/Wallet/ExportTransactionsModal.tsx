import Modal from "@/Components/General/Modal";

export default function ExportTransactionsModal({ isOpen, onClose }: { isOpen: boolean; onClose: () => void }) {
    return (
        <Modal isOpen={isOpen} onClose={onClose} description="Export Table">
            <Modal.Title>Export Table</Modal.Title>

            <Modal.Body>
                <p>
                    Lorem ipsum, dolor sit amet consectetur adipisicing elit. Error ipsum repellat odit fuga, atque
                    dolorem unde minus quas tenetur iure? Veritatis iusto accusantium vel! Officiis laudantium odit
                    ullam enim autem.m
                </p>
                <p>
                    Lorem ipsum, dolor sit amet consectetur adipisicing elit. Error ipsum repellat odit fuga, atque
                    dolorem unde minus quas tenetur iure? Veritatis iusto accusantium vel! Officiis laudantium odit
                    ullam enim autem.m
                </p>
                <p>
                    Lorem ipsum, dolor sit amet consectetur adipisicing elit. Error ipsum repellat odit fuga, atque
                    dolorem unde minus quas tenetur iure? Veritatis iusto accusantium vel! Officiis laudantium odit
                    ullam enim autem.m
                </p>
                <p>
                    Lorem ipsum, dolor sit amet consectetur adipisicing elit. Error ipsum repellat odit fuga, atque
                    dolorem unde minus quas tenetur iure? Veritatis iusto accusantium vel! Officiis laudantium odit
                    ullam enim autem.m
                </p>
            </Modal.Body>

            <Modal.Footer>
                <Modal.FooterButtons>
                    <Modal.CancelButton
                        onClick={() => {
                            console.log("overridden");
                        }}
                    >
                        Cancel
                    </Modal.CancelButton>

                    <Modal.ActionButton>
                        <span>Export</span>
                    </Modal.ActionButton>
                </Modal.FooterButtons>
            </Modal.Footer>
        </Modal>
    );
}
